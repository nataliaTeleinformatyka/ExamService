<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.10.2019
 * Time: 18:19
 */
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Kreait\Firebase\Database\Transaction;


require_once 'C:\xampp\htdocs\examServiceProject\vendor\autoload.php';
class UserRepository /*extends EntityRepository */ implements UserLoaderInterface
{

    protected $db;
    protected $database;
    protected $dbname = 'User';
    private $entityManager = 'User';
    protected $reference;
    /*public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(User::class);
    }*/


   /* public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);

        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');
        //   $firebase = (new Factory)->withServiceAccount($serviceAccount)->create();

        // $this->database = $firebase->getDatabase();
        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
    }*/


    /**
     * UserRepository constructor.
     */
    public function __construct()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');
        /*   $firebase = (new Factory)->withServiceAccount($serviceAccount)->create();

         $this->database = $firebase->getDatabase();*/

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
       // print_r($this->database);
        $this->reference = $this->database->getReference($this->dbname/*'src/Entity/User.php'*/);
       // print_r($this->reference);
    }

   public function getUser(int $userId) {
        if(empty($userId) /*|| isset($userId)*/) { return false; }
       try {
           if ($this->reference->getSnapshot()->hasChild($userId)) {
               return $this->reference->getChild($userId)->getValue();
           } else {
               return false;
           }
       } catch (ApiException $e) {

       }
   }

   public function insert(array $data) {
       if(empty($data) /*|| isset($data)*/) { return false; }

       $actualUserId = $this->getQuantity();

      // foreach($data as $key => $value) {
          // $this->database->getReference()->getChild($this->dbname)->getChild($key)->set($value);
           $this->reference/*->getChild($this->dbname)*/->getChild($actualUserId)->set([
               //$actualUserId => [
               'username' => $data[0],
               'password' => $data[1],
               'first_name' => $data[2],
               'last_name' => $data[3],
               'email' => $data[4],
               'role' => $data[5],
               'last_login' => $data[6],
               'last_password_change' => $data[7],
               'date_registration' => $data[8]
           // ]
           ]);
       //}
       return true;
   }

    public function delete(int $userId) {
        if(empty($userId) /*|| isset($userId)*/) { return false; }

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

    /**
     * Loads the user for the given username.
     *
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     */
    public function loadUserByUsername($username)
    {
        // TODO: Implement loadUserByUsername() method.
    }
}
/*$users = new UserRepository();
var_dump($users);
var_dump($users->insert([
    'username','password','first_name','last_name','email','role','last_login','last_password_change','date_registration'
]));*/
/*var_dump($users->insert([
    'admin','admin','','','','admin','','',''
]));*/
//var_dump($users->getQuantity());
//var_dump($users->get(5));
//var_dump($users->delete(1));