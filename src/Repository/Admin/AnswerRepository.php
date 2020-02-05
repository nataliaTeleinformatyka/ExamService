<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Answer;

class AnswerRepository {
    protected $reference;

    public function __construct() {
        $database = new DatabaseConnection();
        $this->reference = $database->getReference('Exam');
    }

    public function getAnswer(int $examId, int $questionId, int $answerId) {
        if ($this->reference->getSnapshot()->getChild($examId)->getChild("Question")->getChild($questionId)) {
            return $this->reference->getSnapshot()->getChild($examId)->getChild("Question")->getChild($questionId)
                ->getChild("Answer")->getChild($answerId)->getValue();
        } else {
            return 0;
        }
    }

    public function insert(int $examId, int $questionId, array $data) {
        if (empty($data))
            return false;

        $actualAnswerId = $this->getNextId($examId,$questionId);

        $this->reference->getChild($examId)
            ->getChild("Question")->getChild($questionId)->getChild("Answer")->getChild($actualAnswerId)->set([
                'id' => $actualAnswerId,
                'exam_id' => $examId,
                'question_id' => $questionId,
                'content' => $data[0],
                'is_true' => $data[1],
            ]);
        return true;
    }

    public function update(array $data, int $examId, int $questionId, int $id) {
        if (empty($data))
            return false;

        $this->reference->getChild($examId)
            ->getChild("Question")->getChild($questionId)->getChild("Answer")->getChild($id)->update([
                'content' => $data[0],
                'is_true' => $data[1],
            ]);
        return true;
    }

    public function delete(int $examId, int $questionId, int $answerId) {
        $this->reference->getChild($examId)->getChild("Question")
            ->getChild($questionId)->getChild("Answer")->getChild($answerId)->remove();
        return true;
    }

    public function getQuantity(int $examId, int $questionId) {
        return $this->reference->getSnapshot()->getChild($examId)->getChild("Question")
            ->getChild($questionId)->getChild("Answer")->numChildren();
    }

    public function getIdAnswers(int $examId,int $questionId) {
        $answerReference= $this->reference->getChild($examId)->getChild("Question")->getChild($questionId)->getChild("Answer")
            ->getSnapshot()->getReference();
        if($answerReference->getSnapshot()->hasChildren()==NULL){
            return 0;
        } else {
            return $answerReference->getChildKeys();
        }
    }

    public function find(int $answerId){
        $information = $this->reference->getSnapshot()->getChild($_SESSION['exam_id'])
            ->getChild("Question")->getChild($_SESSION['question_id'])
            ->getChild("Answer")->getChild($answerId)->getValue();

        $answer = new Answer([]);
        $answer->setContent($information['content']);
        $answer->setIsTrue($information['is_true']);
        return $answer;
    }

    public function getNextId($examId, $questionId) {
        $answersId = $this->getIdAnswers($examId,$questionId);
        if($answersId!=0){
            $answersAmount = count($answersId);
        } else {
            $answersAmount=0;
        }
        switch ($answersAmount) {
            case 0:{
                $maxNumber = 0;
                break;
            }
            case 1:{
                $maxNumber=$answersId[0]+1;
                break;
            }
            default:{
                $maxNumber=$answersId[0];
                for($i=1;$i<$answersAmount;$i++){
                    if($maxNumber<=$answersId[$i]){
                        $maxNumber =$answersId[$i];
                    }
                }
                $maxNumber=$maxNumber+1;
            }
        }
        return $maxNumber;
    }
}