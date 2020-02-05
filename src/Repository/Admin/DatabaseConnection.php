<?php

namespace App\Repository\Admin;

use App\Entity\Admin\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class DatabaseConnection {
    public $database;
    public $reference;
    protected $serviceAccount;
    protected $factory;

    public function __construct() {
        $this->serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

        $this->factory = (new Factory)
            ->withServiceAccount($this->serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $this->factory->createDatabase();
    }

    public function getReference($dbname) {
        return $this->reference=$this->database->getReference($dbname);
    }

    public function getAuthentication(){
        return (new Factory)
            ->withServiceAccount($this->serviceAccount)
            ->createAuth();
    }

    public function createAdmin() {
        $userRepository = new UserRepository();
        $isAdmin = $userRepository->existAdmin();
        if(!$isAdmin){
            $user = new User([]);
            $id = $userRepository->getIdNextUser();
            $user->setId($id);
            $user->setRoles("ROLE_ADMIN");
            $user->setPassword('administrator123');
            $user->setGroupOfStudents('-1');
            //$user->setName('administrator');
            $user->setLastName('administrator');
            $user->setEmail('administrator@admin.pl');
            $user->setFirstName('administrator');
            $user->setDateRegistration(new \DateTime('now'));
            $user->setLastLogin(new \DateTime('now'));
            $user->setLastPasswordChange(new \DateTime('now'));
            $values = $user->getAllInformation();

            $userRepository->registerUser($id,'administrator@admin.pl','administrator123');
            $userRepository->insert($id, $values);
        }
    }


}