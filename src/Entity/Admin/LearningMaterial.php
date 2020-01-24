<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\LearningMaterialRepository")
 */

class LearningMaterial extends Entity  implements  EquatableInterface
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
    private $learningMaterialsGroupId;
    /**
     * @Assert\NotBlank
     * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $name_of_content;

    /**
     * @Assert\Type("Boolean")
     * @ORM\Column(type="boolean")
     */
    private $is_required;
    /**
     * @ORM\Column(type="string")
     */
    private $attachment;

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
    public function getNameOfContent()
    {
        return $this->name_of_content;
    }

    /**
     * @param mixed $name_of_content
     */
    public function setNameOfContent($name_of_content): void
    {
        $this->name_of_content = $name_of_content;
    }

    /**
     * @return mixed
     */
    public function getIsRequired()
    {
        return $this->is_required;
    }

    /**
     * @param mixed $is_required
     */
    public function setIsRequired($is_required): void
    {
        $this->is_required = $is_required;
    }

    /**
     * @return mixed
     */
    public function getLearningMaterialsGroupId()
    {
        return $this->learningMaterialsGroupId;
    }

    /**
     * @param mixed $learningMaterialsGroupId
     */
    public function setLearningMaterialsGroupId($learningMaterialsGroupId): void
    {
        $this->learningMaterialsGroupId = $learningMaterialsGroupId;
    }

    /**
     * @return mixed
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param mixed $attachment
     */
    public function setAttachment($attachment): void
    {
        $this->attachment = $attachment;
    }

    public function getAllInformation(){
        $data = [$this->id,$this->name,$this->name_of_content,$this->is_required,$this->learningMaterialsGroupId];
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