<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 15.12.2019
 * Time: 21:43
 */

namespace App\Repository\Admin;

use App\Entity\Admin\Exam;
use App\Entity\Admin\Result;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class ResultRepository
{
    protected $db;
    protected $database;
    protected $dbname = 'Result';
    private $entityManager = 'Result';
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

    public function getResult(int $resultId)
    {
        try {
            if ($this->reference->getSnapshot()->hasChild($resultId)) {
                return $this->reference->getChild($resultId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }


    public function getAllResults()
    {
        $resultId = $this->getQuantity();
        if (empty($resultId) /*|| isset($userId)*/) {
            return 0;
        }
        for ($i = 0; $i < $resultId; $i++) {
            try {
                if ($this->reference->getSnapshot()->hasChild($i)) {
                    $data[$i] = $this->reference->getChild($i)->getValue();
                    return $data;
                } else {
                    return 0;
                }
            } catch (ApiException $e) {}
        }
    }

    public function insert(array $data)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }

        $actualResultId = $this->getQuantity();

        $this->reference
            ->getChild($actualResultId)->set([
                //$actualUserId => [
                'id' => $actualResultId,
                'user_id' => $data[0],
                'exam_id' => $data[1],
                'number_of_attempt' => $data[2],
                'points' => $data[3],
                'is_passed' => $data[4],
                'date_of_resolve_exam' => $data[5]
            ]);
        return true;
    }
    public function update(array $data, int $id) {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }

        $this->reference
            ->getChild($id)->update([
                'user_id' => $data[0],
                'exam_id' => $data[1],
                'number_of_attempt' => $data[2],
                'points' => $data[3],
                'is_passed' => $data[4],
                'date_of_resolve_exam' => $data[5]
            ]);
        return true;
    }

    public function delete(int $resultId)
    {
        try {
            if ($this->reference->getSnapshot()->hasChild($resultId)) {
                $this->reference->getChild($resultId)->remove();
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
    public function find(int $resultId){
        $information = $this->reference->getChild($resultId)->getValue();
        $result = new Result([]);
        $result->setUserId($information['user_id']);
        $result->setExamId($information['exam_id']);
        $result->setNumberOfAttempt($information['number_of_attempt']);
        $result->setPoints($information['points']);
        $result->setIsPassed($information['is_passed']);
        $result->setDateOfResolveExam($information['date_of_resolve_exam']);
        return $result;
    }
}