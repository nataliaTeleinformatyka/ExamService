<?php

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 17.11.2019
 * Time: 11:32
 */

class User  extends Entity
{
    private $id;
    private $username;
    private $password;
    private $first_name;
    private $last_name;
    private $email;
    private $role;
    private $last_login;
    private $last_password_change;
    private $date_registration;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param mixed $last_login
     */
    public function setLastLogin($last_login): void
    {
        $this->last_login = $last_login;
    }

    /**
     * @return mixed
     */
    public function getLastPasswordChange()
    {
        return $this->last_password_change;
    }

    /**
     * @param mixed $last_password_change
     */
    public function setLastPasswordChange($last_password_change): void
    {
        $this->last_password_change = $last_password_change;
    }

    /**
     * @return mixed
     */
    public function getDateRegistration()
    {
        return $this->date_registration;
    }

    /**
     * @param mixed $date_registration
     */
    public function setDateRegistration($date_registration): void
    {
        $this->date_registration = $date_registration;
    }



}