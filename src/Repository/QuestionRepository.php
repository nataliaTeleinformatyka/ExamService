<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 13:56
 */

namespace App\Repository;


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

    public function getQuestion(int $questionId)
    {
        //  if(empty($userId) /*|| isset($userId)*/) { return false; } // jesli damy to wowczas nie pobiera 1 rekordu bazy
        try {
            if ($this->reference->getSnapshot()->hasChild($questionId)) {
                return $this->reference->getChild($questionId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }

    public function getAllExams()
    {
        $questionId = $this->getQuantity();
        if (empty($questionId) /*|| isset($userId)*/) {
            return 0;
        }
        for ($i = 0; $i < $questionId; $i++) {
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
//todo: dodawanie nowego pytania do wybranego uprzednio egzaminu
    public function insert(array $data)
    {
        if (empty($data) /*|| isset($data)*/) {
            return false;
        }

        $actualUserId = $this->getQuantity();

        $this->reference->getChild("User")
            ->getChild($actualUserId)->set([
                'content' => $data[0],
                'max_answers' => $data[1],
                'is_multichoice' => $data[2],
                'is_file' => $data[3],
            ]);
        return true;
    }

    public function delete(int $questionId)
    {
        if (empty($questionId) /*|| isset($userId)*/) {
            return false;
        }

        try {
            if ($this->reference->getSnapshot()->hasChild($questionId)) {
                $this->reference->getChild($questionId)->remove();
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