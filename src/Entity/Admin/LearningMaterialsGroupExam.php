<?php

namespace App\Entity\Admin;

use Doctrine\ORM\Mapping as ORM;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Admin\LearningMaterialsGroupExamRepository")
 */
class LearningMaterialsGroupExam extends Entity {
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
    private $learning_materials_group_id;
    /**
     * @Assert\Type("Integer")
     * @ORM\Column(type="integer")
     */
    private $exam_id;

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
    public function getLearningMaterialsGroupId() {
        return $this->learning_materials_group_id;
    }

    /**
     * @param mixed $learning_materials_group_id
     */
    public function setLearningMaterialsGroupId($learning_materials_group_id): void {
        $this->learning_materials_group_id = $learning_materials_group_id;
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

    public function getAllInformation() {
        $data = [$this->learning_materials_group_id,$this->exam_id];
        return $data;
    }
}