<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 07.01.2020
 * Time: 18:12
 */

namespace App\Controller;

use App\Entity\Admin\Result;
use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\QuestionRepository;
use App\Repository\Admin\ResultRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    /**
     * @Route("studentExam/result", name="result")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $examId = $_SESSION['exam_id'];
        $questionAmount = $_SESSION['questionsAmount'];

        $answerRepo = new AnswerRepository();
        $questionRepo = new QuestionRepository();
        $points =0;

$data = "UUUU RESULT CONYTOLLER";

       for($i=0;$i<$questionRepo->getQuantity($examId);$i++) {
           if (isset($_COOKIE['amountOfAnswers' . $i])) {
               $allAnswersAmountFromExam = $_COOKIE['amountOfAnswers' . $i];
               $answersAmount = $_COOKIE['userAnswerAmount' . $i];

               $amount = 0;
               $trueUserAnswer = true;

               for ($k = 0; $k < $questionRepo->getQuantity($examId); $k++) {
                   if (isset($_COOKIE['answerId' . $i . $k])) {
                       $answersId[$k] = $_COOKIE['answerId' . $i . $k];
                       // $answersContent = $_COOKIE['answerContent'.$i.$k];
                        print_r($examId." I ".$i." answer ".$answersId[$k]);

                       $answer = $answerRepo->getAnswer($examId, $i, $answersId[$k]);
                        print_r($answer);
                       if ($answer['is_true']) {
                           $trueAnswer[$k] = $answer['content'];
                           $amount++; //ile prawidlowych odpowiedzi w odp wyslanych do usera
                       }
                   }

                   if ($answersAmount == $amount) {
                       //for ($j = 0; $j < $answersAmount; $j++) {
                       for ($j = 0; $j < $questionRepo->getQuantity($examId); $j++) {
                           if (isset($_COOKIE['userAnswer' . $i . $j])) {
                               $userAnswers[$j] = $_COOKIE['userAnswer' . $i . $j];
                               //for ($m = 0; $m < $amount; $m++) {
                               //  print_r($userAnswers[$j]);
                               // print_r(" TRUE ");
                               //print_r($trueAnswer[$j]);
                               if ($userAnswers[$j] == $trueAnswer[$j] and $trueUserAnswer == true) {
                                   $trueUserAnswer = true;
                                   // print_r(" TAAAK ");
                               } else {
                                   $trueUserAnswer = false;
                                   //   print_r(" NIEEE ");
                               }
                               //}
                           }
                       }
                       if ($trueUserAnswer) $points++;
                   }
               }
              // print_r($points);
           }
       }
        return $this->render('studentResult.html.twig', array(
            'data' => $data

        ));
       // return $this->redirectToRoute('resultList');
    }
    /**
     * @Route("resultList", name="resultList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resultListCreate()
    {

        $resultInformation = new ResultRepository();
        $id = $resultInformation->getQuantity();
        if ($id > 0) {
            $info = true;
            for ($i = 0; $i < $id; $i++) {
                $results = $resultInformation->getResult($i);

                $tplArray[$i] = array(
                    'id' => $results['id'],
                    'user_id' => $results['user_id'],
                    'exam_id' => $results['exam_id'],
                    'number_of_attempt' => $results['number_of_attempt'],
                    'points' => $results['points'],
                    'is_passed' => $results['is_passed'],
                    'date_of_resolve_exam' => $results['date_of_resolve_exam']
                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'id' => '',
                'user_id' => '',
                'exam_id' => '',
                'number_of_attempt' => '',
                'points' => '',
                'is_passed' => '',
                'date_of_resolve_exam' => ''
            );
        }

       /* return $this->render('resultList.html.twig', array(
            'data' => $tplArray,
            'information' => $info,

        ));*/
    }
//* @ParamConverter("POST", class="Entity:Exam")
  /*

    public function editExam(Request $request, Result $result)
    {
        // $repository = $this->getDoctrine()->getRepository(Exam::class);
        /*print_r($id);
        $examEn= new Exam([]);
        $examrepo = new ExamRepository();
        $efxam = $examrepo->getExam($id);
        print_r($efxam);*/
        //  $exam = new Exam([]);
/*
        $resultInformation = new ResultRepository();
        $resultId = (int)$request->attributes->get('id');
        $results = $resultInformation->getResult($resultId);
        //   print_r($exams);
        //print_r($_SESSION['user_id']);
        //print_r($examId);
        //  print_r($exams['exam_id']);
        $resultInfoArray = array(
            'id' => $results['id'],
            'user_id' => $results['user_id'],
            'exam_id' => $results['exam_id'],
            'number_of_attempt' => $results['number_of_attempt'],
            'points' => $results['points'],
            'is_passed' => $results['is_passed'],
            'date_of_resolve_exam' => $results['date_of_resolve_exam']
        );
        //  $exam->setId($examId);
        //$exam->setName($exams['name']);
        //print_r($exam);
        $form = $this->createForm(ExamType::class, $result);
        $form->handleRequest($request);
        //  var_dump($form);
        $exams = $form->getData();
        //print_r($exams);
        if ($form->isSubmitted() && $form->isValid()) {
            $exams = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $examValue = $request->attributes->get('id');
            print_r($examValue);

            $values = $exam->getAllInformation();
            $repositoryExam = new ExamRepository();
            $repositoryExam->update($values,$examId);
            print_r($values);
            // return $this->redirectToRoute('examList');
        }
        return $this->render('examAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$examInfoArray,
            'examId' => $examId
        ]);
    }
*/
    /**
     * @param Request $request
     * @Route("/deleteResult/{result}", name="deleteResult")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteResult(Request $request)
    {
        $id = $request->attributes->get('result');
        $repo = new ResultRepository();

   //     return $this->redirectToRoute('resultList');
    }
}
