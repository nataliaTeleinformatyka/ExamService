<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 07.12.2019
 * Time: 22:27
 */

namespace App\Repository\Admin;


use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class LearningMaterialRepository {
  protected $db;
    protected $database;
    protected $dbname = 'LearningMaterial';
    private $entityManager = 'LearningMaterial';
    protected $reference;

    public function __construct()
{
    $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

    $factory = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

    $storage = (new Factory())->createStorage();
    $storageClient = $storage->getStorageClient();
    $defaultBucket = $storage->getBucket();
    $anotherBucket = $storage->getBucket('learning_material');

    $this->database = $factory->createDatabase();
    $this->reference = $this->database->getReference($this->dbname);
}

    public function getLearningMaterial(int $materialsGroupId,int $materialId)
{
    $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");

    try {
        if ($learningMaterialsGroupReference->getSnapshot()->getChild($materialsGroupId)->hasChild("LearningMaterial")) {
            return $learningMaterialsGroupReference->getSnapshot()->getChild($materialsGroupId)
                ->getChild("LearningMaterial")->getChild($materialId)->getValue();
        } else {
            return 0;
        }
    } catch (ApiException $e) {

    }
}

    public function insert( int $learningMaterialsGroupId, array $data)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }
        $materialId = $this->getQuantity($learningMaterialsGroupId);
        $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");

        $learningMaterialsGroupReference->getChild($learningMaterialsGroupId)
            ->getChild("LearningMaterial")->getChild($materialId)->set([
                'id' => $materialId,
                'learning_materials_group_id' => $learningMaterialsGroupId,
                'name' => $data[1],
                'name_of_content' => $data[2],
                'is_required' => $data[3]
            ]);
        return true;
    }

    public function delete(int $learningMaterialsGroupId, int $materialId)
    {
        $learningMaterialsGroupReference = $this->database->getReference("LearningMaterialsGroup");

        try {
            if ($learningMaterialsGroupReference->getSnapshot()->getChild($learningMaterialsGroupId)
                ->hasChild("LearningMaterial")) {
                $learningMaterialsGroupReference->getChild($learningMaterialsGroupId)
                    ->getChild("LearningMaterial")->getChild($materialId)->remove();
                return true;
            } else {
                return false;
            }
        } catch (ApiException $e) {
        }
    }

    public function getQuantity(int $learningMaterialsGroupId)
    {
        try {
            $examReference = $this->database->getReference("LearningMaterialsGroup");

            return $examReference->getSnapshot()->getChild($learningMaterialsGroupId)
                ->getChild("LearningMaterial")->numChildren();
        } catch (ApiException $e) {
        }
    }
}