<?php

namespace App\Repository\Admin;

use App\Entity\Admin\LearningMaterialsGroup;

class LearningMaterialsGroupRepository {
    protected $reference;

    public function __construct() {
        $database = new DatabaseConnection();
        $this->reference = $database->getReference('LearningMaterialsGroup');
    }

    public function getLearningMaterialsGroup(int $materialsGroupId) {
        if ($this->reference->getSnapshot()->hasChild($materialsGroupId)) {
            return $this->reference->getChild($materialsGroupId)->getValue();
        } else
            return 0;
    }

    public function insert(array $data) {
        if (empty($data))
            return false;

        $materialsGroupId = $this->getNextId();

        $this->reference
            ->getChild($materialsGroupId)->set([
                'learning_materials_groups_id' => $materialsGroupId,
                'name_of_group' => $data[1],
            ]);
        return true;
    }

    public function update(array $data,int $groupId) {
        if (empty($data))
            return false;

        $this->reference
            ->getChild($groupId)->update([
                'name_of_group' => $data[1],
            ]);
        return true;
    }

    public function delete(int $materialsGroupId) {
        if ($this->reference->getSnapshot()->hasChild($materialsGroupId)) {
            $this->reference->getChild($materialsGroupId)->remove();
            return true;
        } else
            return false;
    }

    public function getQuantity() { return $this->reference->getSnapshot()->numChildren(); }

    public function getLearningMaterialsGroupId() {
        if($this->reference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else
            return  $this->reference->getChildKeys();
    }

    public function find(int $groupId) {
        $information = $this->reference->getChild($groupId)->getValue();
        $info = new LearningMaterialsGroup([]);
        $info->setNameOfGroup($information['name_of_group']);
        return $info;
    }

    public function getNextId() {
        $learningMaterialsGroupId= $this->getLearningMaterialsGroupId();
        if($learningMaterialsGroupId!=0){
            $learningMaterialsGroupAmount = count($learningMaterialsGroupId);
        } else
            $learningMaterialsGroupAmount=0;

        switch ($learningMaterialsGroupAmount) {
            case 0:{
                $maxNumber = 0;
                break;
            }
            case 1:{
                $maxNumber=$learningMaterialsGroupAmount[0]+1;
                break;
            }
            default:{
                $maxNumber=$learningMaterialsGroupId[0];
                for($i=1;$i<$learningMaterialsGroupAmount;$i++){
                    if($maxNumber<=$learningMaterialsGroupId[$i]){
                        $maxNumber =$learningMaterialsGroupId[$i];
                    }
                }
                $maxNumber=$maxNumber+1;
            }
        }
        return $maxNumber;
    }
}
