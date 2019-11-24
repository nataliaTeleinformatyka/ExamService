<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 17:20
 */

namespace App\Repository;


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

    public function getExam(int $materialsGroupId)
    {
        //  if(empty($userId) /*|| isset($userId)*/) { return false; } // jesli damy to wowczas nie pobiera 1 rekordu bazy
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
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }

        $materialsGroupId = $this->getQuantity();

        $this->reference
            ->getChild($materialsGroupId)->set([
                'name_of_group' => $data[0],
            ]);
        return true;
    }

    public function delete(int $materialsGroupId)
    {
        if (empty($materialsGroupId) /*|| isset($userId)*/) {
            return false;
        }

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
}
