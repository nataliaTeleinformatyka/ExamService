<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 17:20
 */

namespace App\Repository\Admin;


use App\Entity\Admin\LearningMaterialsGroup;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class LearningMaterialsGroupRepository
{
    protected $db;
    protected $database;
    protected $dbname = 'LearningMaterialsGroup';
    private $entityManager = 'LearningMaterialsGroup';
    protected $reference;

    public function __construct()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
        $this->reference = $this->database->getReference($this->dbname);
    }

    public function getLearningMaterialsGroup(int $materialsGroupId)
    {
        try {
            if ($this->reference->getSnapshot()->hasChild($materialsGroupId)) {
                return $this->reference->getChild($materialsGroupId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }

    public function getAllExams()
    {
        $materialsGroupId = $this->getQuantity();
        if (empty($materialsGroupId) /*|| isset($userId)*/) {
            return 0;
        }
        for ($i = 0; $i < $materialsGroupId; $i++) {
            try {
                if ($this->reference->getSnapshot()->hasChild($i)) {
                    $data[$i] = $this->reference->getChild($i)->getValue();
                    return $data;
                } else {
                    return 0;
                }
            } catch (ApiException $e) {

            }
        }
    }

    public function insert(array $data)
    {
        if (empty($data)) {
            return false;
        }

        $materialsGroupId = $this->getNextId();

        $this->reference
            ->getChild($materialsGroupId)->set([
                'learning_materials_groups_id' => $materialsGroupId,
                'name_of_group' => $data[1],
            ]);
        return true;
    }
    public function update(array $data,int $groupId)
    {
        if (empty($data)) {
            return false;
        }

        $this->reference
            ->getChild($groupId)->update([
                'name_of_group' => $data[1],
            ]);
        return true;
    }
    public function delete(int $materialsGroupId)
    {
        try {
            if ($this->reference->getSnapshot()->hasChild($materialsGroupId)) {
                    $this->reference->getChild($materialsGroupId)->remove();
                return true;
            } else {
                return false;
            }
        } catch (ApiException $e) {
        }
    }

    public function getQuantity()
    {
        try {
            return $this->reference->getSnapshot()->numChildren();
        } catch (ApiException $e) {
        }
    }

    public function getLearningMaterialsGroupId() {
        if($this->reference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else {
            return  $this->reference->getChildKeys();
        }
    }

    public function find(int $groupId){
        $information = $this->reference->getChild($groupId)->getValue();
        $info = new LearningMaterialsGroup([]);
        $info->setNameOfGroup($information['name_of_group']);
        return $info;
    }

    public function getNextId(){
        $learningMaterialsGroupId= $this->getLearningMaterialsGroupId();
        if($learningMaterialsGroupId!=0){
            $learningMaterialsGroupAmount = count($learningMaterialsGroupId);
        } else {
            $learningMaterialsGroupAmount=0;
        }
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
