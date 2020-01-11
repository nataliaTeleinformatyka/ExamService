<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 07.01.2020
 * Time: 18:12
 */

namespace App\Controller;

use App\Entity\Admin\Result;
use App\Repository\Admin\ResultRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    /**
     * @Route("result", name="result")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
      // session_start();
        $r = $request;
        print_r($_COOKIE['questionsAmount']);
        print_r($r);
        //$repository = $this->getDoctrine()->getRepository(Exam::class);
        //$result = new Result([]);
        //print_r($_COOKIE['questionAmount']);



$data = "UUUU RESULT CONYTOLLER";
       // print_r($_SESSION['questionsAmount']);
    //    print_r($_COOKIE['questionsAmount']);
       for($i=0;$i<$_SESSION['questionsAmount'];$i++){

       // $answersAmount = $_COOKIE['userAnswerAmount'];
        /*if($answersAmount!=0) {
            for ($j = 0; $j < $answersAmount; $j++) {
                print_r($_COOKIE['userAnswer'] . $i . $j);
            }
        }*/
    }
      //  $_COOKIE['questionAmount'];
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
