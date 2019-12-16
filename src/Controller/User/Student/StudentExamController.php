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

class StudentExamController extends AbstractController
{
    /**
     * @Route("studentExam/{userExamId}/{questionId}", name="studentExam")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    //todo: losowanie pytan i wyswietlanie odpowiedzi ktore sa active
    //jesli mniej active niz wymaganych to losowo pobierane
    public function studentExamCreate(Request $request) {
        $userExamId = $request->attributes->get('userExamId');
        $questionAmount= $request->attributes->get('questionId');

        $userExamRepo = new UserExamRepository();
        $userExam = $userExamRepo->getUserExam($userExamId);
        $examId = $userExam['exam_id'];

        $examRepo = new ExamRepository();
        $questionRepo = new QuestionRepository();
        $answerRepo = new AnswerRepository();

        $examInfo=$examRepo->getExam($examId);
        $maxQuestions = $examInfo['max_questions'];
        $questionAmount=$questionRepo->getQuantity($examId);

        for($i=0;$i<$questionAmount;$i++){
            $questions=$questionRepo->getQuestion($examId,$i);
            $questionId[$i] = $questions['id'];
            $isMultichoice[$i] = $questions['is_multichoice'];
            $maxAnswers[$i] = $questions['max_answers'];
            $nameOfFile[$i] = $questions['name_of_file'];
            $content[$i] = $questions['content'];
        }

        if($questionAmount <= $maxQuestions){
           $questionContent = $content;
        } else {
            $numbers[] = $this->random($questionAmount, $maxQuestions);
            for ($j = 0; $j < $maxQuestions; $j++) {
                $questionContent = $content[$numbers[$j]];
            }
        }
        print_r($questionContent);
        $fp = fopen("tmp\questions.json", "w");
        //fwrite($fp, json_encode($response));
     //   fclose($fp);
        return $this->render('studentExam.html.twig', array(
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