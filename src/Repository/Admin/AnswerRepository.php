<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 15:47
 */

namespace App\Repository\Admin;


use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class AnswerRepository
{

    protected $db;
    protected $database;
    protected $dbname = 'Answer';
    private $entityManager = 'Answer';
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

    public function getAnswer(int $examId, int $questionId, int $answerId)
    {
        $examReference = $this->database->getReference("Exam");

        //  if(empty($userId) /*|| isset($userId)*/) { return false; } // jesli damy to wowczas nie pobiera 1 rekordu bazy
        try {
            if ($examReference->getSnapshot()->getChild($examId)->getChild("Question")->getChild($questionId)) {
                return $examReference->getSnapshot()->getChild($examId)->getChild("Question")->getChild($questionId)
                    ->getChild("Answer")->getChild($answerId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }

    public function insert(int $examId, int $questionId, array $data)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }

        $actualAnswerId = $this->getQuantity($examId,$questionId);

        $examReference = $this->database->getReference("Exam");

        $examReference->getChild($examId)
            ->getChild("Question")->getChild($questionId)->getChild("Answer")->getChild($actualAnswerId)->set([
                'exam_id' => $examId,
                'question_id' => $questionId,
                'content' => $data[0],
                'is_true' => $data[1],
                'is_active' => $data[2]
            ]);
        return true;
    }

    public function delete(int $examId, int $questionId, int $answerId)
    {
        if (empty($answerId) /*|| isset($userId)*/) {
            return false;
        }
        $examReference = $this->database->getReference("Exam");

        try {
           // if ($examReference->getSnapshot()->getChild($examId)->getChild("Question")->getChild($questionId)->hasChild("Answer")) {
                $examReference->getChild($examId)->getChild("Question")
                    ->getChild($questionId)->getChild("Answer")->getChild($answerId)->remove();
                return true;
            /*} else {
                return false;
            }*/
        } catch (ApiException $e) {
        }
    }

    public function getQuantity(int $examId, int $questionId)
    {
        try {
            $examReference = $this->database->getReference("Exam");

            return $examReference->getSnapshot()->getChild($examId)->getChild("Question")
                ->getChild($questionId)->getChild("Answer")->numChildren();
        } catch (ApiException $e) {
        }
    }
}