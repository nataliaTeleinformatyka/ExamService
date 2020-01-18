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
        //$repository = $this->getDoctrine()->getRepository(Exam::class);
        $exam = new UserExam([]);

        $form = $this->createForm(UserExamType::class, $exam);
        $form->handleRequest($request);
        $exam = $form->getData();
        print_r($exam);
        if ($form->isSubmitted() && $form->isValid()) {

            $exam = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $exam->getAllInformation();
            $repositoryExam = new UserExamRepository();
            $repositoryExam->insert($values);

            return $this->redirectToRoute('userExamList');
        }

        return $this->render('userExamAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("userExamList", name="userExamList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userExamListCreate()
    {
        $userExamRepository = new UserExamRepository();

        $userExamsId = $userExamRepository->getIdUserExams();

        if($userExamsId!=0){
            $userExamAmount = count($userExamsId);
        } else {
            $userExamAmount=0;
        }

        if ($userExamAmount > 0) {
            $info = true;
            for ($i = 0; $i < $userExamAmount; $i++) {
                $userExam = $userExamRepository->getUserExam($userExamsId[$i]);
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
     * @param UserExam $userExam
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("userExamEdit/{userExamId}", name="userExamEdit")
     */
    public function editUserExam(Request $request, UserExam $userExam) {

        $examInformation = new UserExamRepository();
        $userExamId = (int)$request->attributes->get('userExamId');
        $userExams = $examInformation->getUserExam($userExamId);

        $examInfoArray = array(
            'user_exam_id' => $userExams['user_exam_id'],
            'user_id' => $userExams['user_id'],
            'exam_id' => $userExams['exam_id'],
            'start_access_time' => $userExams['start_access_time'],
            'end_access_time' => $userExams['end_access_time']
        );

        $form = $this->createForm(UserExamType::class, $userExam);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            $exams = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $examValue = $request->attributes->get('id');

            $values = $userExam->getAllInformation();
            $repositoryExam = new UserExamRepository();
            $repositoryExam->update($values,$userExamId);

            // return $this->redirectToRoute('examList');
        }
        return $this->render('userExamAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$examInfoArray,
            'userExamId' => $userExamId
        ]);
    }

    /**
     * @param Request $request
     * @Route("/deleteUserExam/{userExamId}", name="deleteUserExam")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteUserExam(Request $request)
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
