<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 03.12.2019
 * Time: 13:14
 */

namespace App\Repository\Admin;


use App\Entity\Admin\UserExam;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class UserExamRepository
{
    private $dbname= "UserExam";
    protected $db;
    protected $database;
    private $entityManager = 'UserExam';
    protected $reference;

    public function __construct() {
        $serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\examServiceProject\secret\examservicedatabase-88ff116bf2b0.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://examservicedatabase.firebaseio.com/');

        $this->database = $factory->createDatabase();
        $this->reference = $this->database->getReference($this->dbname);
    }

    public function getUserExam(int $userExamId) {
        if ($this->reference->getSnapshot()->hasChild($userExamId)) {
            return $this->reference->getChild($userExamId)->getValue();
        } else {
            return 0;
        }
    }

    public function insert(array $data) {
        if (empty($data)) {
            return false;
        }
        $time = new \DateTime('1970-01-01');
        $maxNumber = $this->nextUserExamId();
        $this->reference
            ->getChild($maxNumber)->set([
                'user_exam_id' => $maxNumber,
                'user_id' => $data[0],
                'exam_id' => $data[1],
                'date_of_resolve_exam' => $time,
            ]);
        return true;
    }

    public function delete(int $userExamId) {
        if ($this->reference->getSnapshot()->hasChild($userExamId)) {
            $this->reference->getChild($userExamId)->remove();
            return true;
        } else {
            return false;
        }
    }

    public function getQuantity() { return $this->reference->getSnapshot()->numChildren(); }

    public function getIdUserExams() {
        if($this->reference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else {
            return $this->reference->getChildKeys();
        }
    }

    public function isUserExamForExamId(int $examId) {
        for ($i = 0; $i < $this->getQuantity(); $i++) {
            $userExam = $this->getUserExam($i);
            if ($userExam['exam_id'] == $examId) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function update(array $data, int $id) {
        if (empty($data)) {
            return false;
        }

        $this->reference
            ->getChild($id)->update([
                'date_of_resolve_exam' => $data[0],
            ]);
        return true;
    }

    public function find(int $userExamId){
        $information = $this->reference->getChild($userExamId)->getValue();
        $userExam = new UserExam([]);
       // $userExam->setDateOfResolveExam($information['date_of_resolve_exam']);


        return $userExam;
    }

    public function nextUserExamId(){
        $userExamsId= $this->getIdUserExams();
        if($userExamsId!=0){
            $userExamsAmount = count($userExamsId);
        } else {
            $userExamsAmount=0;
        }
        switch ($userExamsAmount) {
            case 0:{
                $maxNumber = 0;
                break;
            }
            case 1:{
                $maxNumber=$userExamsAmount[0]+1;
                break;
            }
            default:{
                $maxNumber=$userExamsId[0];
                for($i=1;$i<$userExamsAmount;$i++){
                    if($maxNumber<=$userExamsId[$i]){
                        $maxNumber =$userExamsId[$i];
                    }
                }
                $maxNumber=$maxNumber+1;
            }
        }
        return $maxNumber;
    }
}