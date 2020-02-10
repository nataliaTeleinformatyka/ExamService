<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\LearningMaterialsGroupRepository")
 */

class LearningMaterialsGroup extends Entity {
    /**
     * @Assert\Type("Integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @Assert\NotBlank
     * @Assert\Type("String")
     * @ORM\Column(type="string")
     */
    private $name_of_group;
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
    public function getNameOfGroup() {
        return $this->name_of_group;
    }

    /**
     * @param mixed $name_of_group
     */
    public function setNameOfGroup($name_of_group): void {
        $this->name_of_group = $name_of_group;
    }


    public function getAllInformation() {
        $data = [$this->id,$this->name_of_group];
        return $data;
    }
}