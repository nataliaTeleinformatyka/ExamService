<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 13:56
 */

namespace App\Repository\Admin;


use App\Entity\Admin\Question;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class QuestionRepository
{
    protected $db;
    protected $database;
    protected $dbname = 'Question';
    private $entityManager = 'Question';
    protected $reference;

    //todo: Exam -> question -> answer
    //todo: Exam -> question -> learning material question

    public function __construct()
    {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
        $this->reference = $this->database->getReference($this->dbname);
    }

    public function getQuestion(int $idExam, int $questionId)
    {
        $examReference = $this->database->getReference("Exam");

        //  if(empty($userId) /*|| isset($userId)*/) { return false; } // jesli damy to wowczas nie pobiera 1 rekordu bazy
        try {
           if ($examReference->getSnapshot()->getChild($idExam)->hasChild("Question")) {
                return $examReference->getSnapshot()->getChild($idExam)->getChild("Question")->getChild($questionId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }

    public function insert( int $idExam, array $data)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }
        $questionId = $this->getQuantity($idExam);
        $examReference = $this->database->getReference("Exam");

        $examReference->getChild($idExam)
            ->getChild("Question")->getChild($questionId)->set([
                'id_exam' => $idExam,
                'content' => $data[0],
                'max_answers' => $data[1],
                'is_multichoice' => $data[2],
                'is_file' => $data[3]
            ]);
        return true;
    }

    public function delete(int $idExam, int $questionId)
    {
        if (empty($questionId) /*|| isset($userId)*/) {
            return false;
        }
        $examReference = $this->database->getReference("Exam");

        try {
            if ($examReference->getSnapshot()->getChild($idExam)->hasChild("Question")) {
                $examReference->getChild($idExam)->getChild("Question")->getChild($questionId)->remove();
                return true;
            } else {
                return false;
            }
        } catch (ApiException $e) {
        }
    }

    public function getQuantity(int $idExam)
    {
        try {
            $examReference = $this->database->getReference("Exam");

            return $examReference->getSnapshot()->getChild($idExam)->getChild("Question")->numChildren();
        } catch (ApiException $e) {
        }
    }
}