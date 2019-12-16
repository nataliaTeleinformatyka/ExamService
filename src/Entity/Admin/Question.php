<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 11:58
 */

namespace App\Entity\Admin;


use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\QuestionRepository")
 */

class Question extends Entity  implements EquatableInterface
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
     * @Assert\Type("Boolean")
     * @ORM\Column(type="boolean")
     */
    private $is_multichoice;
    /**
     * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $name_of_file;
    /**
     * @ORM\Column(type="string")
     */
    private $file;
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
    public function getIdExam()
    {
        return $this->exam_id;
    }

    /**
     * @param mixed $exam_id
     */
    public function setIdExam($exam_id): void
    {
        $this->exam_id = $exam_id;
    }
    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getMaxAnswers()
    {
        return $this->max_answers;
    }

    /**
     * @param mixed $max_answers
     */
    public function setMaxAnswers($max_answers): void
    {
        $this->max_answers = $max_answers;
    }

    /**
     * @return mixed
     */
    public function getisMultichoice()
    {
        return $this->is_multichoice;
    }

    /**
     * @param mixed $is_multichoice
     */
    public function setIsMultichoice($is_multichoice): void
    {
        $this->is_multichoice = $is_multichoice;
    }

    /**
     * @return mixed
     */
    public function getExamId()
    {
        return $this->exam_id;
    }

    /**
     * @param mixed $exam_id
     */
    public function setExamId($exam_id): void
    {
        $this->exam_id = $exam_id;
    }

    /**
     * @return mixed
     */
    public function getNameOfFile()
    {
        return $this->name_of_file;
    }

    /**
     * @param mixed $name_of_file
     */
    public function setNameOfFile($name_of_file): void
    {
        $this->name_of_file = $name_of_file;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }



    public function getAllInformation(){
        $data = [$this->content,$this->max_answers,$this->is_multichoice,$this->name_of_file];
        return $data;
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