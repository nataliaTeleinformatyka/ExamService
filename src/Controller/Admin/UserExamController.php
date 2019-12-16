<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 03.12.2019
 * Time: 13:12
 */

namespace App\Controller\Admin;


use App\Entity\Admin\Exam;
use App\Entity\Admin\UserExam;
use App\Form\Admin\UserExamType;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserExamController  extends AbstractController
{
    /**
     * @Route("userExam", name="userExam")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        //$repository = $this->getDoctrine()->getRepository(UserExam::class);
        $exam = new UserExam([]);

        $form = $this->createForm(UserExamType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userExam = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $exam->getAllInformation();
            $repositoryExam = new UserExamRepository();
           // $repositoryExam->insert($values);

            // return $this->forward($this->generateUrl('user'));
            //return $this->redirectToRoute('userExamList');
        }

        return $this->render('userExamAdd.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("userExamList", name="userExamList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examListCreate()
    {
        $examInformation = new UserExamRepository();
        $id = $examInformation->getQuantity();
        if ($id > 0) {
            $info = true;
            for ($i = 0; $i < $id; $i++) {
                $userExam = $examInformation->getUserExam($i);
                if($userExam['start_access_time']=="NULL") {
                    $startDate = " ";
                } else {
                    $startDate = $userExam['start_access_time'];
                }
                if($userExam['end_access_time']=="NULL"){
                    $endDate = " ";
                } else {
                    $endDate=$userExam['end_access_time'];
                }
                if($userExam['date_of_resolve_exam'] == "NULL"){
                    $resolveDate = " ";
                } else {
                    $resolveDate=$userExam['date_of_resolve_exam'];
                }
                $tplArray[$i] = array(
                    'user_exam_id' => $userExam['user_exam_id'],
                    'user_id' => $userExam['user_id'],
                    'exam_id' => $userExam['exam_id'],
                    'date_of_resolve_exam' => $resolveDate,
                    'start_access_time' => $startDate,
                    'end_access_time' => $endDate
                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'user_exam_id' => "",
                'user_id' => "",
                'exam_id' => "",
                'date_of_resolve_exam' => "",
                'start_access_time' => "",
                'end_access_time' => ""
            );
        }
        print_r($userExam['user_id']);
        if( isset( $_SESSION['information'] ) && count( $_SESSION['information'] ) > 0  ) {
            $infoDelete = $_SESSION['information'];
        } else {
            $infoDelete = "";
        }
        $_SESSION['information'] = array();
        return $this->render('userExamList.html.twig', array(
            'data' => $tplArray,
            'infoDelete' => $infoDelete,
            'information' => $info
        ));
    }

    /**
     * @param Request $request
     * @param Exam $exam
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{exam}", name="edit")
     */
    public function editExam(Request $request/*, Exam $exam*/)
    {
        $repository = $this->getDoctrine()->getRepository(UserExam::class);
        /* $id = $request->attributes->get('exam');
         print_r($id);
         $examEn= new Exam([]);
         $examrepo = new ExamRepository();
         $efxam = $examrepo->getExam($id);
         print_r($efxam);*/
        $exam = new UserExam([]);

        $form = $this->createForm(UserExamType::class, $exam);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();

            return $this->redirectToRoute('edit', [
                'name' => $exam->getName(),
            ]);
        }
        return $this->render('examEdit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @Route("/deleteUserExam/userExamId", name="deleteUserExam")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteExam(Request $request)
    {
        $userExamId = $request->attributes->get('userExamId');
        $repo = new UserExamRepository();
        /*if($isAnswer !=0 ){
            $_SESSION['information'][] = array( 'type' => 'error', 'message' => 'The record cannot be deleted, there are links in the database');

        } else {
            $repo->delete($examId, $questionId);
            $_SESSION['information'][] = array( 'type' => 'ok', 'message' => 'Successfully deleted');

        }*/
        $repo->delete($userExamId);

        //todo: zapytanie czy chce usunac egzamin gdy sa powiazane question i answers
        //todo: nie mozna usunac egzaminu, gdy jest powiazanie userexam, result

        return $this->redirectToRoute('userExamList');
    }
}
