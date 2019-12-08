<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 03.12.2019
 * Time: 13:14
 */

namespace App\Repository\Admin;


use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class UserExamRepository
{
    private $dbname= "UserExam";
    public function __construct()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
        $this->reference = $this->database->getReference($this->dbname);
    }

    public function getUserExam(int $userId, int $examId)
    {
        //  if(empty($userId) /*|| isset($userId)*/) { return false; } // jesli damy to wowczas nie pobiera 1 rekordu bazy
        try {
            if ($this->reference->getSnapshot()->getChild($examId)->hasChild($userId)) {
                return $this->reference->getChild($examId)->getChild($userId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }


   /* public function getAllExams()
    {
        $examId = $this->getQuantity();
        if (empty($examId) /*|| isset($userId)*//*) {
            return 0;
        }
        for ($i = 0; $i < $examId; $i++) {
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
    }*/

    public function insert(array $data)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }

        $actualUserExamId = $this->getQuantity();
        //todo: egzamin -> user -> reszta
        $this->reference
            ->getChild($actualUserExamId)->set([
                //$actualUserId => [
                'user_id' => $data[0],
                'exam_id' => $data[1],
                '$date_of_resolve_exam' => $data[2],
                'start_access_time' => $data[3],
                'end_access_time' => $data[4],
            ]);
        return true;
    }

    public function delete(int $userId,int $examId)
    {
        if (empty($userId /* || $examId */) /*|| isset($userId)*/) {
            return false;
        }
        try {
            if ($this->reference->getSnapshot()->getChild($examId)->hasChild($userId)) {
                $this->reference->getChild($examId)->getChild($userId)->remove();
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