<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 15:47
 */

namespace App\Repository;


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

    public function getAnswer(int $answerId)
    {
        //  if(empty($userId) /*|| isset($userId)*/) { return false; } // jesli damy to wowczas nie pobiera 1 rekordu bazy
        try {
            if ($this->reference->getSnapshot()->hasChild($answerId)) {
                return $this->reference->getChild($answerId)->getValue();
            } else {
                return 0;
            }
        } catch (ApiException $e) {

        }
    }

    public function getAllAnswers()
    {
        $answerId = $this->getQuantity();
        if (empty($answerId) /*|| isset($userId)*/) {
            return 0;
        }
        for ($i = 0; $i < $answerId; $i++) {
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

        $actualAnswerId = $this->getQuantity();

        $this->reference
            ->getChild($actualAnswerId)->set([
                'content' => $data[0],
                'is_true' => $data[1],
                'is_active' => $data[2],
            ]);
        return true;
    }

    public function delete(int $answerId)
    {
        if (empty($answerId) /*|| isset($userId)*/) {
            return false;
        }

        try {
            if ($this->reference->getSnapshot()->hasChild($answerId)) {
                $this->reference->getChild($answerId)->remove();
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