<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 15:47
 */

namespace App\Repository\Admin;


use App\Entity\Admin\Answer;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class AnswerRepository
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

    public function getAnswer(int $examId, int $questionId, int $answerId)
    {
        $examReference = $this->database->getReference("Exam");
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
    public function update(array $data, int $examId, int $questionId, int $id) {
        if (empty($data)) {
            return false;
        }
        $this->reference->getChild($examId)
            ->getChild("Question")->getChild($questionId)->getChild("Answer")->getChild($id)->update([
                'content' => $data[0],
                'is_true' => $data[1],
                'is_active' => $data[2]
            ]);
        return true;

    }
    public function delete(int $examId, int $questionId, int $answerId)
    {
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
    public function find(int $answerId){
        $examReference = $this->database->getReference("Exam");

        $information = $examReference->getSnapshot()->getChild($_SESSION['exam_id'])
            ->getChild("Question")->getChild($_SESSION['question_id'])
            ->getChild("Answer")->getChild($answerId)->getValue();
        $answer = new Answer([]);
        $answer->setContent($information['content']);
        $answer->setIsActive($information['is_active']);
        $answer->setIsTrue($information['is_true']);
        return $answer;
    }
}