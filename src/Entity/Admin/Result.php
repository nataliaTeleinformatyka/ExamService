<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\ResultRepository")
 */
class Result extends Entity {
    /**
     * @Assert\Type("Integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $user_id;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $exam_id;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $number_of_attempt;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $points;
    /**
     * @Assert\Type("Boolean")
     * @ORM\Column(type="boolean")
     */
    private $is_passed;
    /**
     * @Assert\Type("\DateTime")
     * @ORM\Column(type="datetime")
     */
    private $date_of_resolve_exam;

    /**
     * @return mixed
     */
    public function getPoints() {
        return $this->points;
    }

    /**
     * @param mixed $points
     */
    public function setPoints($points): void {
        $this->points = $points;
    }

    /**
     * @return mixed
     */
    public function getIsPassed() {
        return $this->is_passed;
    }

    /**
     * @param mixed $is_passed
     */
    public function setIsPassed($is_passed): void {
        $this->is_passed = $is_passed;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId() {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getExamId() {
        return $this->exam_id;
    }

    /**
     * @param mixed $exam_id
     */
    public function setExamId($exam_id): void {
        $this->exam_id = $exam_id;
    }

    /**
     * @return mixed
     */
    public function getNumberOfAttempt() {
        return $this->number_of_attempt;
    }

    /**
     * @param mixed $number_of_attempt
     */
    public function setNumberOfAttempt($number_of_attempt): void {
        $this->number_of_attempt = $number_of_attempt;
    }

    /**
     * @return mixed
     */
    public function getDateOfResolveExam() {
        return $this->date_of_resolve_exam;
    }

    /**
     * @param mixed $date_of_resolve_exam
     */
    public function setDateOfResolveExam($date_of_resolve_exam): void {
        $this->date_of_resolve_exam = $date_of_resolve_exam;
    }

    public function getAllInformation() {
        $data = [$this->user_id,$this->exam_id,$this->number_of_attempt,$this->points,$this->is_passed,
            $this->date_of_resolve_exam];
        return $data;
    }
}