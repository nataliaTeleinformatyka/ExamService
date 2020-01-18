<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 20.11.2019
 * Time: 10:11
 */

namespace App\Entity\Admin;


use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\ExamRepository")
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
   private $max_questions;
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
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
   private $duration_of_exam;
   //User Id who made exam
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
   private $created_by;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $percentage_passed_exam;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function getLearningRequired()
    {
        return $this->learning_required;
    }

    /**
     * @param boolean $learning_required
     */
    public function setLearningRequired($learning_required): void
    {
        $this->learning_required = $learning_required;
    }

    /**
     * @return string
     */
    public function getAdditionalInformation()
    {
        return $this->additional_information;
    }

    /**
     * @param string $additional_information
     */
    public function setAdditionalInformation($additional_information): void
    {
        $this->additional_information = $additional_information;
    }

    /**
     * @return integer
     */
    public function getMaxQuestions()
    {
        return $this->max_questions;
    }

    /**
     * @param integer $max_questions
     */
    public function setMaxQuestions($max_questions): void
    {
        $this->max_questions = $max_questions;
    }

    /**
     * @return integer
     */
    public function getMaxAttempts()
    {
        return $this->max_attempts;
    }

    /**
     * @param integer $max_attempts
     */
    public function setMaxAttempts($max_attempts): void
    {
        $this->max_attempts = $max_attempts;
    }

    /**
     * @return datetime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param datetime $start_date
     */
    public function setStartDate($start_date): void
    {
        $this->start_date = $start_date;
    }

    /**
     * @return datetime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param datetime $end_date
     */
    public function setEndDate($end_date): void
    {
        $this->end_date = $end_date;
    }

    /**
     * @return mixed
     */
    public function getDurationOfExam()
    {
        return $this->duration_of_exam;
    }

    /**
     * @param mixed $duration_of_exam
     */
    public function setDurationOfExam($duration_of_exam): void
    {
        $this->duration_of_exam = $duration_of_exam;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getPercentagePassedExam()
    {
        return $this->percentage_passed_exam;
    }

    /**
     * @param mixed $percentage_passed_exam
     */
    public function setPercentagePassedExam($percentage_passed_exam): void
    {
        $this->percentage_passed_exam = $percentage_passed_exam;
    }

    public function getAllInformation(){
        $data = [$this->name,$this->learning_required,$this->additional_information,$this->max_questions,$this->max_attempts,
            $this->start_date,$this->end_date,$this->duration_of_exam,$this->created_by,$this->percentage_passed_exam];
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