<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\QuestionRepository")
 */

class Question extends Entity
{
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
    private $exam_id;
    /**
     * @Assert\NotBlank
     * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $content;

    /**
     * @Assert\NotBlank
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $max_answers;

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
    public function getIdExam() {
        return $this->exam_id;
    }

    /**
     * @param mixed $exam_id
     */
    public function setIdExam($exam_id): void {
        $this->exam_id = $exam_id;
    }
    /**
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getMaxAnswers() {
        return $this->max_answers;
    }

    /**
     * @param mixed $max_answers
     */
    public function setMaxAnswers($max_answers): void {
        $this->max_answers = $max_answers;
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

    public function getAllInformation(){
        $data = [$this->content,$this->max_answers];
        return $data;
    }
}