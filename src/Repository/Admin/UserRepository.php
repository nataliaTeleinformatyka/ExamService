<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.10.2019
 * Time: 18:19
 */
namespace App\Repository\Admin;

use App\Entity\Admin\User;

use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $this->reference = $this->database->getReference($this->dbname/*'src/Entity/User.php'*/);
    }

    public function registerUser(int $uid,String $email,String $password,String $username){

            $userProperties = [
                'uid' => $uid,
                'email' => $email,
                'emailVerified' => false,
                'password' => $password,
                'displayName' => $username,
            ];

            $createdUser = $this->auth->createUser($userProperties);
    }

    public function getUsersFromAuthentication() {
        $users = $this->auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);

      return $users;
    }

    public function getUserPasswordFromAuthentication(String $email){
        $user = $this->auth->getUserByEmail($email);
        $data= $user->toArray();
        return $data['passwordHash'];
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

   public function insert(array $data) {
       if(empty($data) /*|| isset($data)*/) { return false; }

       $actualUserId = $this->getQuantity();
       $this->reference->getChild($actualUserId)->set([
           'username' => $data[0],
           'first_name' => $data[2],
           'last_name' => $data[3],
           'email' => $data[4],
           'role' => $data[5],
           'last_login' => $data[6],
           'last_password_change' => $data[7],
           'date_registration' => $data[8],
           'class' => $data[9]
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

    public function getQuantity()
    {
        try {
            return $this->reference->getSnapshot()->numChildren();
        } catch (ApiException $e) {
        }
    }
}
