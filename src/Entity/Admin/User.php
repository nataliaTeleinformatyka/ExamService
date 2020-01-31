<?php
namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\UserRepository")
 */

class User extends Entity
{
    /**
     * @Assert\Type("Integer")
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $first_name;
    /**
     * * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $last_name;
    /**
     * * @Assert\Email()
     * @ORM\Column(type="string")
     */
    private $email;
    /**
     * * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $role;
    /**
     * @Assert\Type("\DateTime")
     * @ORM\Column(type="datetime")
     */
    private $last_login;
    /**
     * @Assert\Type("\DateTime")
     * @ORM\Column(type="datetime")
     */
    private $last_password_change;
    /**
     * @Assert\Type("\DateTime")
     * @ORM\Column(type="datetime")
     */
    private $date_registration;
    /**
     * * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $group_of_students;
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
    /**
     * @return String
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param String $first_name
     */
    public function setFirstName($first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return String
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param String $last_name
     */
    public function setLastName($last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return String
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param String $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }
    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param \DateTime $last_login
     */
    public function setLastLogin($last_login): void
    {
        $this->last_login = $last_login;
    }

    /**
     * @return \DateTime
     */
    public function getLastPasswordChange()
    {
        return $this->last_password_change;
    }

    /**
     * @param \DateTime $last_password_change
     */
    public function setLastPasswordChange($last_password_change): void
    {
        $this->last_password_change = $last_password_change;
    }

    /**
     * @return \DateTime
     */
    public function getDateRegistration()
    {
        return $this->date_registration;
    }

    /**
     * @param \DateTime $date_registration
     */
    public function setDateRegistration($date_registration): void
    {
        $this->date_registration = $date_registration;
    }

    /**
     * @return mixed
     */
    public function getGroupOfStudents()
    {
        return $this->group_of_students;
    }

    /**
     * @param $group_of_students
     */
    public function setGroupOfStudents($group_of_students): void
    {
        $this->group_of_students = $group_of_students;
    }

    public function getAllInformation(){
        $data = [$this->password,$this->first_name,$this->last_name,$this->email,$this->role,
            $this->last_login,$this->last_password_change,$this->date_registration,$this->group_of_students];
        return $data;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */


    public function getRoles()
    {
        $role = $this->role;

        return $role;
    }

    public function setRoles(String $role)
    {
        switch($role) {
            case 'admin':
                $role = 'ROLE_ADMIN';
                break;
            case 'professor':
                $role = 'ROLE_PROFESSOR';
                break;
            case 'student':
                $role = 'ROLE_STUDENT';
                break;
            default: $role = $role;
        }

        $this->role = $role;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }
/*
    /**
     * @see UserInterface
     */
    /*public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }*/

    /**
     * @see UserInterface
     */
    /*public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @return bool
     */
   /* public function isEqualTo(UserInterface $user)
    {
        // TODO: Implement isEqualTo() method.
    }*/
}