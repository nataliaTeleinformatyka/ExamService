<?php

namespace App\Repository\Admin;

class ResultRepository {
    protected $reference;

    public function __construct() {
        $database = new DatabaseConnection();
        $this->reference = $database->getReference('UserExam');
    }

    public function getResult(int $userExamId, int $resultId) {
        if ($this->reference->getSnapshot()->getChild($userExamId)->hasChild("Result")) {
            return $this->reference->getChild($userExamId)->getChild("Result")->getChild($resultId)->getValue();
        } else
            return 0;
    }

    public function insert($userExamId, $data, $examId) {
        if (empty($data))
            return false;

        $actualResultId = $this->getNextId($userExamId);

        $this->reference->getChild($userExamId)->getChild("Result")
            ->getChild($actualResultId)->set([
                'id' => $actualResultId,
                'exam_id' => $examId,
                'points' => $data[3],
                'is_passed' => $data[4],
            ]);
        return true;
    }

    public function delete(int $userExamId,int $resultId) {
        if ($this->reference->getSnapshot()->getChild($userExamId)->hasChild("Result")) {
            $this->reference->getChild($userExamId)->getChild("Result")->getChild($resultId)->remove();
            return true;
        } else
            return false;
    }

    public function getQuantity(int $userExamId) {
        return $this->reference->getSnapshot()->getChild($userExamId)->getChild("Result")->numChildren();
    }

    public function getIdResults(int $userExamId) {
        $resultReference= $this->reference->getChild($userExamId)->getChild("Result")->getSnapshot()->getReference();
        if($resultReference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else
            return $resultReference->getChildKeys();
    }
//todo: THIS
   /*public function getQuantityAttempt($userExamId,$examId, $userId){
        $id = $this->getIdResults($userExamId);
        $idAmount = count($id);
        for($i=0;$i<$idAmount;$i++) {
            if ($this->reference->getChild($userExamId)->getChild("Result")->getSnapshot()->hasChild($id[$i])) {
                $resultInfo = $this->reference->getChild($userExamId)->getChild("Result")->getChild($i)->getValue();
                if($resultInfo['exam_id'] == $examId and $resultInfo['user_id']==$userId){
                    return $resultInfo['number_of_attempt'];
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
    }*/

    public function getNextId($userExamId) {
        $resultsId= $this->getIdResults($userExamId);
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