<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 15.12.2019
 * Time: 20:42
 */

namespace App\Repository\Admin;


use App\Entity\Admin\LearningMaterialsGroupExam;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class LearningMaterialsGroupExamRepository
{
    protected $db;
    protected $database;
    protected $dbname = 'LearningMaterialsGroupExam';
    private $entityManager = 'LearningMaterialsGroupExam';
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

    public function getLearningMaterialsGroupExam(int $id)
    {
        try {
            if ($this->reference->getSnapshot()->hasChild($id)) {
                return $this->reference->getChild($id)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }
    public function insert(array $data)
    {
        if (empty($data)) {
            return false;
        }

        $materialsGroupExamId = $this->getQuantity();

        $this->reference
            ->getChild($materialsGroupExamId)->set([
                'id' => $materialsGroupExamId,
                'learning_materials_group_id' => $data[0],
                'exam_id' => $data[1],
            ]);
        return true;
    }
    public function update(array $data,int $id)
    {
        if (empty($data)) {
            return false;
        }

        $this->reference
            ->getChild($id)->update([
                'learning_materials_group_id' => $data[0],
                'exam_id' => $data[1],
            ]);
        return true;
    }
    public function delete(int $materialsGroupExamId)
    {
        try {
            if ($this->reference->getSnapshot()->hasChild($materialsGroupExamId)) {
                $this->reference->getChild($materialsGroupExamId)->remove();
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
    public function find(int $groupId){
        $information = $this->reference->getChild($groupId)->getValue();
        $info = new LearningMaterialsGroupExam([]);
        $info->setLearningMaterialsGroupId($information['learning_materials_group_id']);
        $info->setExamId($information['exam_id']);
        return $info;
    }
    public function findByGroupId(int $learningMaterialsGroupId) {
        $amount = $this->getQuantity();
        for($i=0;$i<$amount;$i++) {
            $information = $this->reference->getChild($i)->getValue();
            if($information['learning_materials_group_id'] == $learningMaterialsGroupId) {
                return true;
            } else {
                return false;
            }

        }

    }
}