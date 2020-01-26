<?php

namespace App\Controller\User\Student;

use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\QuestionRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StudentExamController extends AbstractController
{
    /**
     * @Route("studentExam/{userExamId}", name="studentExam")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function studentExamCreate(Request $request) {
        $userExamId = $request->attributes->get('userExamId');
        $amount = 0;

        $userExamRepository = new UserExamRepository();
        $examRepository = new ExamRepository();
        $questionRepository = new QuestionRepository();
        $answerRepository = new AnswerRepository();

        $userExam = $userExamRepository->getUserExam($userExamId);
        $examId = $userExam['exam_id'];

        $examInfo=$examRepository->getExam($examId);
        $maxQuestions = $examInfo['max_questions'];

        $questionsId = $questionRepository->getIdQuestions($examId);
        if($questionsId!=0) {
            $questionsAmount = count($questionsId);
        } else {
            $questionsAmount =0;
        }

        if($questionsAmount > $maxQuestions) {
            $questionsAmount=$maxQuestions;
        }
            $numbers = array_rand($questionsId, $questionsAmount);

            for($i=0;$i<$questionsAmount;$i++) {
                $id = $questionsId[$numbers[$i]];
                $questions=$questionRepository->getQuestion($examId,$id);

                setcookie("questionId" . $i, $questions['id']/*$questions['id']*/);
                setcookie("questionMaxAnswers" . $i, $questions['max_answers']);
                setcookie("questionContent" . $i,$questions['content']);

                $answerNumbers = $answerRepository->getIdAnswers($examId, $questions['id']);
                print_r($answerNumbers);
                if ($answerNumbers == 0) {
                    $allAnswersAmount = 0;
                    print_r("JESTEM W ZERZE");
                } else {
                    $allAnswersAmount = count($answerNumbers);
                }
                $ids=array();
                if ($allAnswersAmount > 0) {
                    if ($allAnswersAmount <= $questions['max_answers']){

                        $answerNumber = array_rand($answerNumbers, $allAnswersAmount);

                        for ($j = 0; $j < $allAnswersAmount; $j++) {
                            if($allAnswersAmount>1){
                                $answerId = $answerNumbers[$answerNumber[$j]];
                            } else {
                                $answerId = $answerNumber;
                            }
                            $answerInfo = $answerRepository->getAnswer($examId, $questions['id'], $answerId);
                            $ids[$j] = $answerInfo['id'];

                            setcookie("answerId" . $i . $j, $answerInfo['id']);
                            setcookie("answerContent" . $i . $j, $answerInfo['content']);
                        }
                    }
                }
                setcookie("amountOfAnswers" . $i, $allAnswersAmount);
                setcookie("allAnswers" . $i, json_encode($ids));
            }

        /*} else {
            $numbers=array_rand($questionsId,$maxQuestions);

            for($i=0;$i<$questionsAmount;$i++) {
                $id = $questionsId[$numbers[$i]];
                $questions=$questionRepository->getQuestion($examId,$id);

                setcookie("questionId" . $i, $questions['id']/*$questions['id']*//*);
                setcookie("questionMaxAnswers" . $i, $questions['max_answers']);
                setcookie("questionNameOfFile" . $i,  $questions['name_of_file']);
                setcookie("questionContent" . $i,$questions['content']);

                $answerNumbers = $answerRepository->getIdAnswers($examId, $questions['id']);
                if ($answerNumbers == 0) {
                    $allAnswersAmount = 0;
                } else {
                    $allAnswersAmount = count($answerNumbers);
                }
                $ids=array();
                if ($allAnswersAmount > 0) {
                    if ($allAnswersAmount <= $questions['max_answers']){

                        $answerNumber = array_rand($answerNumbers, $allAnswersAmount);

                        for ($j = 0; $j < $allAnswersAmount; $j++) {
                            if($allAnswersAmount>1){
                                $answerId = $answerNumbers[$answerNumber[$j]];
                            } else {
                                $answerId = $answerNumber;
                            }
                            $answerInfo = $answerRepository->getAnswer($examId, $questions['id'], $answerId);
                            $ids[$j] = $answerInfo['id'];

                            setcookie("answerId" . $i . $j, $answerInfo['id']);
                            setcookie("answerContent" . $i . $j, $answerInfo['content']);
                        }
                    }
                }
                setcookie("amountOfAnswers" . $i, $allAnswersAmount);
                setcookie("allAnswers" . $i, json_encode($ids));
            }
        }*/
        setcookie("questionAmount", $questionsAmount);

        $actualHour = date('H')*60;
        $actualMinutes = date('i');

        $durationOfExam = $examInfo['duration_of_exam']+$actualHour+$actualMinutes;
        setcookie("accessTime",$durationOfExam);


        $_SESSION['questionsAmount'] = $questionsAmount;
        $_SESSION['exam_id']= $examId;
        setcookie("user_exam_id",$userExamId);

        return $this->render('studentExam.html', array(
        ));
    }


 /*   public function random($amountOfQuestions, $maxQuestions) {
         $randomNumbers[0] =  rand(0, $amountOfQuestions);
         $amountOfRandomedNumbers = 1;

         for ($i = 1; $i < $maxQuestions; $i++) {
             do {
                 $number = rand(0, $amountOfQuestions);
                 $isRandom = true;

                 for ($j = 0; $j < $amountOfRandomedNumbers; $j++) {
                     if ($number == $randomNumbers[$j]) $isRandom = false;
                 }
                 if ($isRandom == true) {
                     $randomNumbers[$amountOfRandomedNumbers] = $number;
                     $amountOfRandomedNumbers++;
                 }

             } while ($isRandom != true);
         }
         return $randomNumbers;
    }*/
}