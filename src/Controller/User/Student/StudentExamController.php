<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 13.12.2019
 * Time: 13:40
 */

namespace App\Controller\User\Student;


use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\QuestionRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
define('FILENAME','questions.json');
class StudentExamController extends AbstractController
{
    /**
     * @Route("studentExam/{userExamId}", name="studentExam")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    //todo: losowanie pytan i wyswietlanie odpowiedzi ktore sa active
    //jesli mniej active niz wymaganych to losowo pobierane
    public function studentExamCreate(Request $request) {
        $userExamId = $request->attributes->get('userExamId');
$amount = 0;

        $userExamRepo = new UserExamRepository();
        $examRepo = new ExamRepository();
        $questionRepo = new QuestionRepository();
        $answerRepo = new AnswerRepository();

        $userExam = $userExamRepo->getUserExam($userExamId);
        $examId = $userExam['exam_id'];


        $examInfo=$examRepo->getExam($examId);
        $maxQuestions = $examInfo['max_questions'];

        $questionsAmount=$questionRepo->getQuantity($examId);


       // $accessTime = date("H",strtotime($durationOfExam['date']))*60 + date("i",strtotime($durationOfExam['date']));

        for($i=0;$i<$questionsAmount;$i++){
            $questions=$questionRepo->getQuestion($examId,$i);
            if($questions['id'] != NULL or $questions['id']=="0") {
                $questionId[$amount] = $questions['id'];
                $isMultichoice[$amount] = $questions['is_multichoice'];
                $maxAnswers[$amount] = $questions['max_answers'];
                $nameOfFile[$amount] = $questions['name_of_file'];
                $content[$amount] = $questions['content'];
                $amount++;
            }
        }

        if($amount <= $maxQuestions){
            $numbers=$this->random($amount-1,$amount);

            for($i=0;$i<$amount;$i++)
            {
                setcookie("questionId" . $i, $questionId[$numbers[$i]]/*$questions['id']*/);
                setcookie("questionIsMultichoice" . $i, $isMultichoice[$numbers[$i]]);
                setcookie("questionMaxAnswers" . $i, $maxAnswers[$numbers[$i]]);
                setcookie("questionNameOfFile" . $i, $nameOfFile[$numbers[$i]]);
                setcookie("questionContent" . $i, $content[$numbers[$i]]);

                    $allAnswersAmount= $answerRepo->getQuantity($examId, $questionId[$numbers[$i]]);
                $answersAmount = 0;

                    for ($k = 0; $k < $allAnswersAmount; $k++) {
                        $answerInfo = $answerRepo->getAnswer($examId, $questionId[$numbers[$i]], $k);
                        // if($answerContent['is_true'] or $answerContent['is_active']) {
                        if ($answerInfo['id'] != NULL or $answerInfo['id'] == "0") {
                            $answersId[$k] = $answerInfo['id'];
                            $answers[$k] = $answerInfo['content'];
                            $answersAmount++;
                        }

                    }
                    if($answersAmount <= $maxAnswers) {
                        $answerNumber = $this->random($answersAmount-1, $answersAmount);
                        print_r($answerNumber);
                        print_r("answer amount ".$answersAmount);
                        for ($j = 0; $j < $answersAmount; $j++) {
                            setcookie("answerId".$i.$j, $answersId[$answerNumber[$j]]); // (numer pytania,numer odpowiedzi);
                            setcookie("answerContent".$i.$j, $answers[$answerNumber[$j]]);
                        }
                        setcookie("amountOfAnswers".$i, $answersAmount);
                }
                    //todo: answer must be active jezeli ma byc wyswietllona, true musi byc active  and true


            }

        } else {
            $numbers[] = $this->random($amount-1, $maxQuestions-1);
            for ($j = 0; $j < $maxQuestions; $j++) {
                $questions = $questionRepo->getQuestion($examId, $j);
                setcookie("questionId".$j, $questions['id'] );
                setcookie("questionMaxAnswers".$j, $questions['max_answers'] );
                setcookie("questionNameOfFile".$j, $questions['name_of_file'] );
                setcookie("questionContent".$j, $questions['content'] );
                //$questionContent = $content[$numbers[$j]];
            }
        }
        setcookie("questionAmount", $amount);

        $actualHour = date('H')*60;
        $actualMinutes = date('i');

        $durationOfExam = $examInfo['duration_of_exam']+$actualHour+$actualMinutes;
        setcookie("accessTime",$durationOfExam);


        $_SESSION['questionsAmount'] = $amount;
        $_SESSION['exam_id']= $examId;

        return $this->render('studentExam.html', array(
        ));
    }


    public function random($amountOfQuestions, $maxQuestions) {
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
    }
}