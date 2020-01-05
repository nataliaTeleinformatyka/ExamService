<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 20.11.2019
 * Time: 12:34
 */

namespace App\Controller\Admin;


use App\Entity\Admin\Exam;
use App\Entity\Admin\User;
use App\Form\Admin\ExamType;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\QuestionRepository;
use App\Repository\Admin\UserExamRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExamController extends AbstractController
{
    /**
     * @Route("exam", name="exam")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        //$repository = $this->getDoctrine()->getRepository(Exam::class);
        $exam = new Exam([]);

        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);
        $exam = $form->getData();
        print_r($exam);
        if ($form->isSubmitted() && $form->isValid()) {

            $exam = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $exam->getAllInformation();
            $repositoryExam = new ExamRepository();
            $repositoryExam->insert($values);

             return $this->redirectToRoute('examList');
        }

        return $this->render('examAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
//todo: zapisywac do bazy przez kogo stworzony exam, zeby nie mogli przypisywac uczniow do nie swojego egzaminu
    /**
     * @Route("examList", name="examList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examListCreate()
    {

        $examInformation = new ExamRepository();
        $id = $examInformation->getQuantity();
        if ($id > 0) {
            $info = true;
            for ($i = 0; $i < $id; $i++) {
                $exams = $examInformation->getExam($i);
                if ($exams['learning_required'] == 1) {
                    $is_required = true;
                } else {
                    $is_required = false;
                }
                $tplArray[$i] = array(
                    'id' => $i,
                    'name' => $exams['name'],
                    'learning_required' => $is_required,
                    'max_questions' => $exams['max_questions'],
                    'max_attempts' => $exams['max_attempts'],
                    'duration_of_exam' => $exams['duration_of_exam']['date'], //todo: only time!!
                    'created_by' => $exams['created_by'],
                    'start_date' => $exams['start_date']['date'],
                    'end_date' => $exams['end_date']['date'],
                    'additional_information' => $exams['additional_information']
                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'id' => "",
                'name' => "",
                'learning_required' => "",
                'max_questions' => "",
                'max_attempts' => "",
                'duration_of_exam' => "",
                'created_by' => "",
                'start_date' => "",
                'end_date' => "",
                'additional_information' => ""
            );
        }
        if( isset( $_SESSION['information'] ) && count( $_SESSION['information'] ) > 0  ) {
            $infoDelete = $_SESSION['information'];
        } else {
            $infoDelete = "";
        }
        $_SESSION['information'] = array();
        return $this->render('examList.html.twig', array(
            'data' => $tplArray,
            'information' => $info,
            'infoDelete' => $infoDelete

        ));
    }
//* @ParamConverter("POST", class="Entity:Exam")
    /**
     * @param Request $request
     * @param Exam $exam

     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editExam/{id}", name="editExam")
     */
    public function editExam(Request $request, Exam $exam)
    {
       // $repository = $this->getDoctrine()->getRepository(Exam::class);
         /*print_r($id);
         $examEn= new Exam([]);
         $examrepo = new ExamRepository();
         $efxam = $examrepo->getExam($id);
         print_r($efxam);*/
      //  $exam = new Exam([]);

        $examInformation = new ExamRepository();
        $examId = (int)$request->attributes->get('id');
        $exams = $examInformation->getExam($examId);
     //   print_r($exams);
        //print_r($_SESSION['user_id']);
        //print_r($examId);
      //  print_r($exams['exam_id']);
        $examInfoArray = array(
           // 'id' => $exams['exam_id'],
            'name' => $exams['name'],
            'learning_required' => $exams['learning_required'],
            'max_questions' => $exams['max_questions'],
            'max_attempts' => $exams['max_attempts'],
            'duration_of_exam' => $exams['duration_of_exam']['date'],
            'start_date' => $exams['start_date']['date'],
            'end_date' => $exams['end_date']['date'],
            'additional_information' => $exams['additional_information']
        );
      //  $exam->setId($examId);
        //$exam->setName($exams['name']);
        //print_r($exam);
        $form = $this->createForm(ExamType::class, $exam);
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

    /**
     * @param Request $request
     * @Route("/delete/{exam}", name="delete")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteExam(Request $request)
    {
        $id = $request->attributes->get('exam');
        $repo = new ExamRepository();
        $questionRepo = new QuestionRepository();
        $userExamRepo = new UserExamRepository();

        $isQuestion = $questionRepo->getQuantity($id);
        $isUserExam = $userExamRepo->isUserExamForExamId($id);
        if($isQuestion !=0 or $isUserExam==false){
            $_SESSION['information'][] = array( 'type' => 'error', 'message' => 'The record cannot be deleted, there are links in the database');

        } else {
            $repo->delete($id);
            $_SESSION['information'][] = array( 'type' => 'ok', 'message' => 'Successfully deleted');

        }


        return $this->redirectToRoute('examList');
    }
}
