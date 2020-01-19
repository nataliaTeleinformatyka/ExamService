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

        $userExamRepository = new UserExamRepository();
        $examRepository = new ExamRepository();
        $questionRepository = new QuestionRepository();
        $answerRepository = new AnswerRepository();

        $userExam = $userExamRepository->getUserExam($userExamId);
        $examId = $userExam['exam_id'];

        $examInfo=$examRepository->getExam($examId);
        $maxQuestions = $examInfo['max_questions'];

      //  $questionsAmount=$questionRepository->getQuantity($examId);
        $questionsId = $questionRepository->getIdQuestions($examId);

        $questionsAmount = count($questionsId);

print_r(" EXAM ID ".$examId);
       // $accessTime = date("H",strtotime($durationOfExam['date']))*60 + date("i",strtotime($durationOfExam['date']));
        for($i=0;$i<$questionsAmount;$i++){
            $answersIds = $answerRepository->getIdAnswers($examId,$questionsId[$i]);

            //if($questions['id'] != NULL or $questions['id']=="0") {
               /* $questionId[$i] = $questions['id'];
                $maxAnswers[$i] = $questions['max_answers'];
                $nameOfFile[$i] = $questions['name_of_file'];
                $content[$i] = $questions['content'];*/
              //  $amount++;
            //}
        //    print_r(" i ".$i." w ".$questions);
        }

        if($questionsAmount/*$amount*/ <= $maxQuestions){
            $numbers=array_rand($questionsId,$questionsAmount);
            //$numbers=$this->random($questionsAmount,$questionsAmount-1);//random($amount-1,$amount);
            print_r($questionsId);
print_r($numbers);
            for($i=0;$i<$questionsAmount;$i++) {
                $id = $questionsId[$numbers[$i]];
                $questions=$questionRepository->getQuestion($examId,$id);

                print_r($id);
                setcookie("questionId" . $i, $questions['id']/*$questions['id']*/);
                setcookie("questionMaxAnswers" . $i, $questions['max_answers']);
                setcookie("questionNameOfFile" . $i,  $questions['name_of_file']);
                setcookie("questionContent" . $i,$questions['content']);

                $answerNumbers = $answerRepository->getIdAnswers($examId, $questions['id']);
                if ($answerNumbers == 0) {
                    $allAnswersAmount = 0;
                    print_r(" TUUU ".$i." lll");
                } else {
                    $allAnswersAmount = count($answerNumbers);
                    print_r(" TUTAJ ".$i." pppldld ");
                }
                print_r( $allAnswersAmount );
                if ($allAnswersAmount > 0) {
                   // for ($k = 0; $k < $allAnswersAmount; $k++) {
                      //  $answerInfo = $answerRepository->getAnswer($examId, $questions['id'], $answerNumbers[$k]);
                        // if($answerContent['is_true'] or $answerContent['is_active']) {
                        //if ($answerInfo['id'] != NULL or $answerInfo['id'] == "0") {
                      //  $answersId[$k] = $answerInfo['id'];
                      //  $answers[$k] = $answerInfo['content'];
                        //  $answersAmount++;
                        //}
                   // print_r($answerInfo);
                   // print_r($answerNumbers);
                 //   }



                    if ($allAnswersAmount <= $questions['max_answers']){//} and $allAnswersAmount>1) {

                        $answerNumber = array_rand($answerNumbers, $allAnswersAmount);//$this->random($allAnswersAmount-1, $allAnswersAmount);
                       print_r(" answeNumber ");
                        print_r($answerNumber);


                        for ($j = 0; $j < $allAnswersAmount; $j++) {
                            print_r(" idIDid".$j);
                            if($allAnswersAmount>1){
                                $answerId = $answerNumbers[$answerNumber[$j]];
                            } else {
                                $answerId = $answerNumber;
                            }
                        print_r($answerId);
                            $answerInfo = $answerRepository->getAnswer($examId, $questions['id'], $answerId);


                           // print_r(" KKKKKWIEKSZERROWNE ".$answerNumber[$j]);
                            setcookie("answerId" . $i . $j, $answerInfo['id']); // (numer pytania,numer odpowiedzi);
                            setcookie("answerContent" . $i . $j, $answerInfo['content']);
                        }
                    }
                    /*if($allAnswersAmount==0){
                        print_r(" ROWNEZERO");
                        setcookie("answerId" . $i . $k, $answersId[$answerNumber[$k]]); // (numer pytania,numer odpowiedzi);
                        setcookie("answerContent" . $i . $k, $answers[$answerNumber[$k]]);
                        setcookie("amountOfAnswers" . $i, $allAnswersAmount);

                    }*/

                }
                setcookie("amountOfAnswers" . $i, $allAnswersAmount);


            }

        } else {
            $numbers[] = $this->random($amount-1, $maxQuestions-1);
            for ($j = 0; $j < $maxQuestions; $j++) {
                $questions = $questionRepository->getQuestion($examId, $j);
                setcookie("questionId".$j, $questions['id'] );
                setcookie("questionMaxAnswers".$j, $questions['max_answers'] );
                setcookie("questionNameOfFile".$j, $questions['name_of_file'] );
                setcookie("questionContent".$j, $questions['content'] );
                //$questionContent = $content[$numbers[$j]];
            }
        }
        setcookie("questionAmount", $questionsAmount);

        $actualHour = date('H')*60;
        $actualMinutes = date('i');

        $durationOfExam = $examInfo['duration_of_exam']+$actualHour+$actualMinutes;
        setcookie("accessTime",$durationOfExam);


        $_SESSION['questionsAmount'] = $questionsAmount;
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