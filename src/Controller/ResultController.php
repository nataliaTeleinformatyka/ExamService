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
use App\Repository\Admin\ExamRepository;
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
        $userId = $_SESSION['user_id'];
        $questionAmount = $_SESSION['questionsAmount'];

        $answerRepo = new AnswerRepository();
        $questionRepo = new QuestionRepository();
        $points = 0;
        $existQuestion = false;
        $questionGoodAmount = $questionRepo->getQuantity($examId);
        $amount = 0;
        $questionsId = $questionRepo->getIdQuestions($examId);
        for ($j = 0; $j < $questionAmount; $j++) {
            $isTrueAnswer=true;
            $question = $questionRepo->getQuestion($examId, $_COOKIE['questionId' . $j]);
           // $answersId = $answerRepo->getIdAnswers($examId, $_COOKIE['questionId' . $j]);
            if (/*$answersId */ $_COOKIE['amountOfAnswers' . $j]== 0 ) {
                $points++;
            } else {
                for ($k = 0; $k < $_COOKIE['amountOfAnswers' . $j]; $k++) {
                    //print_r($_COOKIE['amountOfAnswers' . $j]);
                    $answerInformation = $answerRepo->getAnswer($examId, $_COOKIE['questionId' . $j], $_COOKIE['answerId'.$j.$k]);
                    /*if($answerInformation['is_true']==true){
                        $isTrueAnswer=false;
                    }*/
                    //print_r($answerInformation);
                }
            }


            $questionInformation = $questionRepo->getQuestion($examId, $questionsId[$j]);

            $questionId = $questionInformation['id'];
            $answersId = $answerRepo->getIdAnswers($examId, $questionId);
            //print_r(" AAA ");
            //print_r($answersId);
            if($answersId!=0){
                $answerInformation = $answerRepo->getAnswer($examId, $questionId, $answersId[$j]);
            }else {
                $answerInformation = $answerRepo->getAnswer($examId, $questionId, $answersId);

            }
           // print_r($answerInformation);

            /*if($questionInformation[$j]['is_true']==true){
                $question[$amount] = $questionsId[$j];
                $amount++;
            }*/
        }
        return $this->render('studentResult.html.twig', array(
            'points' => $points

        ));
    }


/*
       for($i=0;$i<$questionAmount;$i++) {

           $trueAnswers=0;
           $amount = 0;
           $trueUserAnswer = true;
           $questionId = $i;

           $questionInfo = $questionRepo->getQuestion($examId,$i);
           /*if($questionInfo['content']==""){
               $i++;
               $questionId++;
               $existQuestion=false;
               $questionGoodAmount++;
           }*//*
/*print_r(" PPPPP ");
           $check=1;
               for ($k = 0; $k < $answerRepo->getQuantity($examId,$i); $k++) {
                   if (isset($_COOKIE['answerId' . $i . $k])) {
                       $answerInformation = $answerRepo->getAnswer($examId,$i,$_COOKIE['answerId' . $i . $k]);
                       if($answerInformation['is_true'] == true) {
                           $trueAnswers++;
                       }
                   //    setcookie ("answerId".$i.$k, "", time() - 3600);
                    //   setcookie ("answerContent".$i.$k, "", time() - 3600);

                   }
                   if($questionInfo['content']=="" and $check==1){
                       $i--;
                   }
                   $check++;

                print_r("   AAAAAAAAAAAAAAAAAAAAA".$i.$k." ");
//                   if ($answersAmount == $amount) {
                   $existQuestion=true;
                    //   for ($j = 0; $j < $answerRepo->getQuantity($examId,$k)/* $answersAmount*//*; $j++) {

                          /* if (isset($_COOKIE['userAnswer' . $i . $k])) {

                               print_r(" question ".$questionId. " answer " .$_COOKIE['userAnswer' . $i . $k]);

                                $answer = $answerRepo->getAnswer($examId,$questionId,$_COOKIE['userAnswer' . $i . $k]);
                                print_r($answer);
                                    if ($answer['is_true'] == true and $trueUserAnswer) {
                                        print_r(" TRUEEEE ");
                                        $amount++;
                                    } else {
                                        print_r("  FALSE  ".$i.$k." ");
                                        $trueUserAnswer = false;
                                    }
                              // setcookie ("userAnswer".$i.$k, "", time() - 3600);

                           }// else {
                               if($amount!=$trueAnswers) {
                                   print_r(" UPS ");
                                   $trueUserAnswer = false;
                               }
                           //}

print_r(" answerTRUE ".$trueUserAnswer);
                      // }
                           /*
                     //  for ($j = 0; $j < $questionRepo->getQuantity($examId); $j++) {
                           if (isset($_COOKIE['userAnswer' . $i . $j])) {
                               $userAnswers[$j] = $_COOKIE['userAnswer' . $i . $j];
                               //for ($m = 0; $m < $amount; $m++) {
                                 print_r($userAnswers[$j]);
                                print_r(" TRUE ");
                               print_r($trueAnswer[$j]);
                               if ($userAnswers[$j] == $trueAnswer[$j] and $trueUserAnswer == true) {
                                   $trueUserAnswer = true;
                                    print_r(" TAAAK ");
                               } else {
                                   $trueUserAnswer = false;
                                      print_r(" NIEEE ");
                               }
                               //}
                           }
                       }
                       if ($trueUserAnswer) $points++;*/
                //   }
               /*}
               if($amount==0 and $trueAnswers>0){
                   $trueUserAnswer=false;

                   print_r(" UUU DALCZEGOTUTAJJESTEM ");
               }
               print_r(" amount ".$amount);
               print_r(" ISTRUE ".$trueAnswers);
               print_r(" exist ".$existQuestion);
           if ($trueUserAnswer){//} and $trueAnswers==$amount and $existQuestion) {
               $points++;
                print_r(" DLACEGO ");
           }
               print_r(" POINTS " . $points);

          // }

       //    setcookie ("userAnswerAmount".$i, "", time() - 3600);
         //  setcookie ("amountOfAnswers", "", time() - 3600);


       }
      //  setcookie ("questionAmount", "", time() - 3600);
        //setcookie ("accessTime", "", time() - 3600);

        $resultRepository = new ResultRepository();
        $examRepository = new ExamRepository();
        $examInformation = $examRepository->getExam($examId);
        $percentagePassedExam = $examInformation['percentage_passed_exam'];
        print_r($percentagePassedExam);
       $id = $resultRepository->getQuantity();
       $numberOfAttempt = $resultRepository->getQuantityAttempt($examId,$userId);

       $isPassed = true;
       $dateOfResolveExam = "NULL";
       $resultRepository->insert($id, $userId, $examId, $numberOfAttempt,$points, $isPassed, $dateOfResolveExam);
        return $this->render('studentResult.html.twig', array(
            'points' => $points

        ));
       // return $this->redirectToRoute('resultList');*/
   /// }
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

        return $this->render('resultList.html.twig', array(
            'data' => $tplArray,
            'information' => $info,

        ));
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
