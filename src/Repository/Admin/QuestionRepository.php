<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Question;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class QuestionRepository
{
    protected $db;
    protected $database;
    protected $dbname = 'Exam';
    protected $reference;

    public function __construct() {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
        $this->reference = $this->database->getReference($this->dbname);
    }

    public function getQuestion(int $examId, int $questionId) {
        $examReference = $this->database->getReference("Exam");

        if ($examReference->getSnapshot()->getChild($examId)->hasChild("Question")) {
            return $examReference->getSnapshot()->getChild($examId)->getChild("Question")->getChild($questionId)->getValue();
        } else {
            return 0;
        }
    }

    public function insert( int $examId, array $data) {
        if (empty($data)) {
            return false;
        }

        $questionRepository = new QuestionRepository();
        $examReference = $this->database->getReference("Exam");

        $maxNumber = $questionRepository->getNextId($examId);

        $examReference->getChild($examId)
            ->getChild("Question")->getChild($maxNumber)->set([
                'id' => $maxNumber,
                'exam_id' => $examId,
                'content' => $data[0],
                'max_answers' => $data[1],
            ]);
        return true;
    }

    public function delete(int $examId, int $questionId) {
        $examReference = $this->database->getReference("Exam");
        if ($examReference->getSnapshot()->getChild($examId)->hasChild("Question")) {
            $examReference->getChild($examId)->getChild("Question")->getChild($questionId)->remove();
            return true;
        } else {
            return false;
        }
    }

    public function getQuantity(int $examId) {
        $examReference = $this->database->getReference("Exam");

        return $examReference->getSnapshot()->getChild($examId)->getChild("Question")->numChildren();
    }

    public function getIdQuestions(int $examId) {
        $examReference = $this->database->getReference("Exam");
        $questionReference= $examReference->getChild($examId)->getChild("Question")->getSnapshot()->getReference();
        if($questionReference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else {
            return $questionReference->getChildKeys();
        }
    }

    public function update(array $data, int $id,int $questionId) {
        if (empty($data)) {
            return false;
        }
        $this->reference ->getChild($id)
                ->getChild("Question")->getChild($questionId)->update([
                'content' => $data[0],
                'max_answers' => $data[1],
            ]);
        return true;
    }

    public function find(int $questionId) {
        $information = $this->reference->getSnapshot()->getChild($_SESSION['exam_id'])
            ->getChild("Question")->getChild($questionId)->getValue();
        $question = new Question([]);
        $question->setContent($information['content']);
        $question->setMaxAnswers($information['max_answers']);
        $question->setNameOfFile($information['name_of_file']);

        return $question;
    }

    public function getNextId($examId) {
        $questionId= $this->getIdQuestions($examId);
        if($questionId!=0){
            $questionsAmount = count($questionId);
        } else {
            $questionsAmount=0;
        }
        switch ($questionsAmount) {
            case 0:{
                $maxNumber = 0;
                break;
            }
            case 1:{
                $maxNumber=$questionsAmount[0]+1;
                break;
            }
            default:{
                $maxNumber=$questionId[0];
                for($i=1;$i<$questionsAmount;$i++){
                    if($maxNumber<=$questionId[$i]){
                        $maxNumber =$questionId[$i];
                    }
                }
                $maxNumber=$maxNumber+1;
            }
        }
        return $maxNumber;
    }
}