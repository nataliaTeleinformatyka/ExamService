<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 12:35
 */

namespace App\Repository\Admin;


use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class ExamRepository
{

    protected $db;
    protected $database;
    protected $dbname = 'Exam';
    private $entityManager = 'Exam';
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

    public function getExam(int $examId)
    {
        //  if(empty($userId) /*|| isset($userId)*/) { return false; } // jesli damy to wowczas nie pobiera 1 rekordu bazy
        try {
            if ($this->reference->getSnapshot()->hasChild($examId)) {
                return $this->reference->getChild($examId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }


    public function getAllExams()
    {
        $examId = $this->getQuantity();
        if (empty($examId) /*|| isset($userId)*/) {
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
    }

    public function insert(array $data)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }

        $actualUserId = $this->getQuantity();

        $this->reference
        ->getChild($actualUserId)->set([
            //$actualUserId => [
            'name' => $data[0],
            'learning_required' => $data[1],
            'additional_information' => $data[2],
            'min_questions' => $data[3],
            'max_attempts' => $data[4],
            'start_date' => $data[5],
            'end_date' => $data[6]
        ]);
        return true;
    }

    public function delete(int $examId)
    {
        if (empty($examId) /*|| isset($userId)*/) {
            return false;
        }
        try {
            if ($this->reference->getSnapshot()->hasChild($examId)) {
                $this->reference->getChild($examId)->remove();
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


    public function find($exam_id){



    }


}