<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\UserExamRepository")
 */

class UserExam extends Entity {

    /**
     * @Assert\Type("Integer")
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $user_exam_id;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $exam_id;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $user_id;
    /**
     * @Assert\Type("\Datetime")
     * @ORM\Column(type="datetime")
     */
    private $date_of_resolve_exam;

    /**
     * @return mixed
     */
    public function getUserExamId() {
        return $this->user_exam_id;
    }

    /**
     * @param mixed $user_exam_id
     */
    public function setUserExamId($user_exam_id): void {
        $this->user_exam_id = $user_exam_id;
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
        $data = [$this->date_of_resolve_exam];
        return $data;
    }
}