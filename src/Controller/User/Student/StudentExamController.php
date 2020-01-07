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

        $userExamRepo = new UserExamRepository();
        $userExam = $userExamRepo->getUserExam($userExamId);
        $examId = $userExam['exam_id'];

        $examRepo = new ExamRepository();
        $questionRepo = new QuestionRepository();
        $answerRepo = new AnswerRepository();

        $examInfo=$examRepo->getExam($examId);
        $maxQuestions = $examInfo['max_questions'];
        $questionsAmount=$questionRepo->getQuantity($examId);

        for($i=0;$i<$questionsAmount;$i++){
            $questions=$questionRepo->getQuestion($examId,$i);
            $questionId[$i] = $questions['id'];
            $isMultichoice[$i] = $questions['is_multichoice'];
            $maxAnswers[$i] = $questions['max_answers'];
            $nameOfFile[$i] = $questions['name_of_file'];
            $content[$i] = $questions['content'];

        }

        if($questionsAmount <= $maxQuestions){
            for($i=0;$i<$questionsAmount;$i++) {
                $questions = $questionRepo->getQuestion($examId, $i);
                if( $questions['id']==$i) {
                    setcookie("questionId" . $i, $i/*$questions['id']*/);
                    setcookie("questionIsMultichoice" . $i, $questions['is_multichoice']);
                    setcookie("questionMaxAnswers" . $i, $questions['max_answers']);
                    setcookie("questionNameOfFile" . $i, $questions['name_of_file']);
                    setcookie("questionContent" . $i, $questions['content']);
                    print_r($questions);
                    $questionId[$i] = $questions['id'];

                }

                if($questions['id'] != NULL or $questions['id']=="0"){
                    $answersAmount= $answerRepo->getQuantity($examId,$questions['id']);
                    $amount = 0;
                    for ($k = 0; $k < $answersAmount; $k++) {
                        $answerContent = $answerRepo->getAnswer($examId, $questions['id'], $k);
                        if($answerContent['is_true'] or $answerContent['is_active']) {
                            $amount++;
                            setcookie("answerId" . $i/*$questions['id']*/ . $k, $k); // (numer pytania,numer odpowiedzi);
                            setcookie("answerContent" . $i/*$questions['id']*/ . $k, $answerContent['content']);
                            setcookie("amountOfAnswers" .$i/* $questions['id']*/, $amount);
                        }
                    } //todo: nie wysylac czy poprawna odpowiedz, wysylac wszystkie true+aktywne(do max answers)
                    //todo: answer must be active jezeli ma byc wyswietllona, true musi byc active  and true

                }
            }
        } else {
            $numbers[] = $this->random($questionsAmount, $maxQuestions);
            for ($j = 0; $j < $maxQuestions; $j++) {
                $questions = $questionRepo->getQuestion($examId, $j);
                setcookie("questionId".$j, $questions['id'] );
                setcookie("questionIsMultichoice".$j, $questions['is_multichoice'] );
                setcookie("questionMaxAnswers".$j, $questions['max_answers'] );
                setcookie("questionNameOfFile".$j, $questions['name_of_file'] );
                setcookie("questionContent".$j, $questions['content'] );
                //$questionContent = $content[$numbers[$j]];
            }
        }
        setcookie("questionAmount", $questionsAmount);




       /* $fp = fopen("FILENAME", "w");
        //$isFile = file_exists("questions.json");
        fputs($fp, json_encode($questions));
        fclose($fp);
        $dane = fread(fopen("FILENAME", "r"), filesize("FILENAME"));*/

        return $this->render('studentExam.html', array(
        ));
    }


    public function random($amountOfQuestions, $maxQuestions) {
         $randomNumbers[0] =  rand(0, $amountOfQuestions);
         $amountOfRandomedNumbers = 1;

         for ($i = 0; $i < $maxQuestions; $i++) {
             do {
                 $number = rand(0, $amountOfQuestions);
                 $isRandom = true;

                 for ($j = 0; $j < $amountOfRandomedNumbers; $j++) {
                     if ($number == $randomNumbers[$j]) $isRandom = false;
                 }
                 if ($isRandom == true) {
                     $amountOfRandomedNumbers++;
                     $randomNumbers[$amountOfRandomedNumbers] = $number;
                 }

             } while ($isRandom != true);
         }
         return $randomNumbers;
    }
}