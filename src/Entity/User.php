<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GeneratedValue;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;
//use Symfony\Component\Validator\Tests\Constraints as Assert;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 17.11.2019
 * Time: 11:32
 */

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */

class User extends Entity implements UserInterface, EquatableInterface
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
    private $username;
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param String $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param String $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
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
     * @return String
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param String $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
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

    public function getAllInformation(){
        $data = [$this->username,$this->password,$this->first_name,$this->last_name,$this->email,$this->role,
            $this->last_login,$this->last_password_change,$this->date_registration];
        return $data;
    }
    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
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
    public function isEqualTo(UserInterface $user)
    {
        // TODO: Implement isEqualTo() method.
    }
}