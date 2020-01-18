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

    public function insert($id, $userId, $examId, $numberOfAttempt,$points, $isPassed, $dateOfResolveExam)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }

        $actualResultId = $this->getNextId();

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

    public function getIdResults()
    {
        if($this->reference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else {
            return  $this->reference->getChildKeys();
        }
    }

    public function getQuantityAttempt($examId, $userId){
        $id = $this->getQuantity();
        for($i=0;$i<$id;$i++) {
            if ($this->reference->getSnapshot()->hasChild($i)) {
                $resultInfo = $this->reference->getChild($i)->getValue();
                if($resultInfo['exam_id'] == $examId and $resultInfo['user_id']==$userId){
                    return $resultInfo['number_of_attempt'];
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
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

    public function getNextId(){
        $resultsId= $this->getIdResults();
        if($resultsId!=0){
            $resultsAmount = count($resultsId);
        } else {
            $resultsAmount=0;
        }
        switch ($resultsAmount) {
            case 0:{
                $maxNumber = 0;
                break;
            }
            case 1:{
                $maxNumber=$resultsAmount[0]+1;
                break;
            }
            default:{
                $maxNumber=$resultsId[0];
                for($i=1;$i<$resultsAmount;$i++){
                    if($maxNumber<=$resultsId[$i]){
                        $maxNumber =$resultsId[$i];
                    }
                }
                $maxNumber=$maxNumber+1;
            }
        }
        return $maxNumber;
    }
}