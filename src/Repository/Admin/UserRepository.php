<?php

namespace App\Repository\Admin;

use App\Entity\Admin\User;

use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

require_once 'C:\xampp\htdocs\examServiceProject\vendor\autoload.php';
class UserRepository
{

    protected $db;
    protected $database;
    protected $dbname = 'User';
    private $entityManager = 'User';
    protected $reference;
    private $auth;
    /**
     * UserRepository constructor.
     */
    public function __construct()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');


        $this->database = $factory->createDatabase();
        $this->auth = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->createAuth();
        $this->reference = $this->database->getReference($this->dbname);
    }

    public function registerUser(int $uid,String $email,String $password){

            $userProperties = [
                'uid' => $uid,
                'email' => $email,
                'emailVerified' => false,
                'password' => $password,
                'displayName' => $email
            ];

            $createdUser = $this->auth->createUser($userProperties);
    }

    public function getUsersFromAuthentication() {
        $users = $this->auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);

      return $users;
    }

  /*  public function getUserPasswordFromAuthentication(String $email){
        $user = $this->auth->getUserByEmail($email);
        $data= $user->toArray();
        return $data['passwordHash'];
    }*/
    public function getUserIdFromAuthentication(String $email){
        $user = $this->auth->getUserByEmail($email);
        $data= $user->toArray();
        return $data['uid'];
    }
   /* public function getUserLastLoginFromAuthentication(String $email){
        $user = $this->auth->getUserByEmail($email);
        $data= $user->toArray();
        $metadata= $data['metadata']->toArray();
       /* foreach($metadata as &$blog) {
            $blogs = get_object_vars($blog);*/
     /*       $date = $metadata['lastLoginAt'];
    //    }
        return $date;
    }*/
    public function deleteUserFromAuthenticationByEmail(string $email){
        $user = $this->auth->getUserByEmail($email);
        $id = $user->uid;
        print_r("ID: ".$user->uid);
        $this->auth->deleteUser(strval($id));
        return true;
    }
    public function deleteUserFromAuthentication(int $id){
        $this->auth->deleteUser(strval($id));
        return true;
    }

    public function editUserPasswordFromAuthentication(int $id,String $password){
        $updatedUser = $this->auth->changeUserPassword(strval($id), $password);
    }

    public function editUserEmailFromAuthentication(int $id,String $email){
        $updatedUser = $this->auth->changeUserEmail(strval($id), $email);
    }

    public function sendResetLinkToEmail(String $email){

        $this->auth->sendPasswordResetEmail($email, 'login');
    }

    public function checkPassword(String $email,String $password){
        return $this->auth->verifyPassword($email, $password);
    }

   public function getUser(int $userId) {
       try {
           if ($this->reference->getSnapshot()->hasChild($userId)) {
               return $this->reference->getChild($userId)->getValue();
           } else {
               return 0;
           }
       } catch (ApiException $e) {

       }
   }
    public function getUserByEmail(string $email) {
        $amount = $this->getQuantity();
        for($i=0;$i<$amount;$i++) {
            $userInfo = $this->reference->getChild($i)->getValue();

            if($userInfo['email'] == $email) {
                return $userInfo;
            }
        }
        return 0;
    }
   public function insert(int $id, array $data) {
       if(empty($data)) {
           return false;
       }

       $this->reference->getChild($id)->set([
           'id' => $id,
           'first_name' => $data[1],
           'last_name' => $data[2],
           'email' => $data[3],
           'role' => $data[4],
           'last_login' => $data[5],
           'last_password_change' => $data[6],
           'date_registration' => $data[7],
           'group_of_students' => $data[8]
       ]);
       return true;
   }

    public function update(array $data, int $id) {
        if (empty($data) ) {
            return false;
        }
        $email = $data[3];
        $idFromAuthentication = $this->getUserIdFromAuthentication($email);
        print_r($idFromAuthentication);
        $user = $this->auth->getUser($idFromAuthentication);
        print_r($user);
        $updatedUser = $this->auth->changeUserPassword($user->uid, $data[0]);
        //$this->editUserPasswordFromAuthentication($idFromAuthentication,$data[0]);
        $this->reference
            ->getChild($id)->update([
                'first_name' => $data[1],
                'last_name' => $data[2],
                'group_of_students' => $data[8]
            ]);
        return true;
    }

    public function delete(int $userId) {
        try {
            if ($this->reference->getSnapshot()->hasChild($userId)) {
                $this->reference->getChild($userId)->remove();
                return true;
            } else {
                return false;
            }
        } catch (ApiException $e) {
        }
    }

    public function getQuantity() {
        try {
            return $this->reference->getSnapshot()->numChildren();
        } catch (ApiException $e) {
        }
    }

    public function getIdUsers() {
        if($this->reference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else {
            return $this->reference->getChildKeys();
        }
    }

    public function getIdNextUser(){
        $usersId = $this->getIdUsers();
        if($usersId!=0){
            $usersAmount = count($usersId);
        } else {
            $usersAmount=0;
        }
        switch ($usersAmount) {
            case 0:{
                $maxNumber = 0;
                break;
            }
            case 1:{
                $maxNumber=$usersId[0]+1;
                break;
            }
            default:{
                $maxNumber=$usersId[0];
                for($i=1;$i<$usersAmount;$i++){
                    if($maxNumber<=$usersId[$i]){
                        $maxNumber =$usersId[$i];
                    }
                }
                $maxNumber=$maxNumber+1;
            }
        }
        return $maxNumber;
    }

    public function find(int $userId){
        $information = $this->reference->getChild($userId)->getValue();
        $user = new User([]);
        $user->setFirstName($information['first_name']);
        $user->setLastName($information['last_name']);
        $user->setEmail($information['email']);

        $user->setGroupOfStudents($information['group_of_students']);

        return $user;
    }
}
