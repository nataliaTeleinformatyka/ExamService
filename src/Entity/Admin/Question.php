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
     * @Assert\Type("Boolean")
     * @ORM\Column(type="boolean")
     */
    private $is_file;

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
        return $this->id_exam;
    }

    /**
     * @param mixed $id_exam
     */
    public function setIdExam($id_exam): void
    {
        $this->id_exam = $id_exam;
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
    public function getisFile()
    {
        return $this->is_file;
    }

    /**
     * @param mixed $is_file
     */
    public function setIsFile($is_file): void
    {
        $this->is_file = $is_file;
    }

    public function getAllInformation(){
        $data = [$this->content,$this->max_answers,$this->is_multichoice,$this->is_file];
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