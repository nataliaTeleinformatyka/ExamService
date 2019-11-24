<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 20.11.2019
 * Time: 10:11
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExamRepository")
 */

class Exam extends Entity  implements  EquatableInterface
{
    /**
     * @Assert\Type("Integer")
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
   private $id;
    /**
     * @Assert\NotBlank
     * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
   private $name;
    /**
     * @Assert\NotBlank
     * @Assert\Type("Boolean")
     * @ORM\Column(type="boolean")
     */
   private $learning_required;
    /**
     * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
   private $additional_information;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
   private $min_questions;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
   private $max_attempts;
    /**
     * @Assert\Type("\DateTime")
     * @ORM\Column(type="datetime")
     */
   private $start_date;
    /**
     * @Assert\Type("\DateTime")
     * @ORM\Column(type="datetime")
     */
   private $end_date;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLearningRequired()
    {
        return $this->learning_required;
    }

    /**
     * @param mixed $learning_required
     */
    public function setLearningRequired($learning_required): void
    {
        $this->learning_required = $learning_required;
    }

    /**
     * @return mixed
     */
    public function getAdditionalInformation()
    {
        return $this->additional_information;
    }

    /**
     * @param mixed $additional_information
     */
    public function setAdditionalInformation($additional_information): void
    {
        $this->additional_information = $additional_information;
    }

    /**
     * @return mixed
     */
    public function getMinQuestions()
    {
        return $this->min_questions;
    }

    /**
     * @param mixed $min_questions
     */
    public function setMinQuestions($min_questions): void
    {
        $this->min_questions = $min_questions;
    }

    /**
     * @return mixed
     */
    public function getMaxAttempts()
    {
        return $this->max_attempts;
    }

    /**
     * @param mixed $max_attempts
     */
    public function setMaxAttempts($max_attempts): void
    {
        $this->max_attempts = $max_attempts;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     */
    public function setStartDate($start_date): void
    {
        $this->start_date = $start_date;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param mixed $end_date
     */
    public function setEndDate($end_date): void
    {
        $this->end_date = $end_date;
    }

    public function getAllInformation(){
        $data = [$this->name,$this->learning_required,$this->additional_information,$this->min_questions,$this->max_attempts,
            $this->start_date,$this->end_date];
        return $data;
    }


    /** @noinspection PhpHierarchyChecksInspection */

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