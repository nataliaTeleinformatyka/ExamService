<?php

namespace App\Controller\Admin;

use App\Entity\Admin\UserExam;
use App\Form\Admin\UserExamType;
use App\Repository\Admin\ResultRepository;
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
    public function new(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");

        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $exam = new UserExam([]);
        $form = $this->createForm(UserExamType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $exam = $form->getData();
            $values = $exam->getAllInformation();
            $repositoryExam = new UserExamRepository();
            $repositoryExam->insert($values);

            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('userExamList');
                        break;
                    }
                case "ROLE_PROFESSOR":
                    {
                        return $this->redirectToRoute('teacherExamInfo', [
                            'exam' =>  $_SESSION['exam_id'],
                        ]);
                        break;
                    }
            }
        }

        return $this->render('userExamAdd.html.twig', [
            'form' => $form->createView(),
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @Route("userExamList", name="userExamList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userExamListCreate() {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");

        switch ($_SESSION['role']) {
            case "ROLE_PROFESSOR": {
                return $this->redirectToRoute('teacherExamList');
                break;
            }
            case "ROLE_STUDENT": {
                return $this->redirectToRoute('studentHomepage');
                break;
            }
        }
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

                $year = date("Y", strtotime($userExam['date_of_resolve_exam']['date']));

                if($year < '2020'){
                    $resolveDate = " ";
                } else {
                    $resolveDate=$userExam['date_of_resolve_exam']['date'];
                }
                $tplArray[$i] = array(
                    'user_exam_id' => $userExam['user_exam_id'],
                    'user_id' => $userExam['user_id'],
                    'exam_id' => $userExam['exam_id'],
                    'date_of_resolve_exam' => $resolveDate,
                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'user_exam_id' => "",
                'user_id' => "",
                'exam_id' => "",
                'date_of_resolve_exam' => "",
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
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

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

            $values = $userExam->getAllInformation();
            $repositoryExam = new UserExamRepository();
            $repositoryExam->update($values,$userExamId);

            switch ($_SESSION['role']) {
                case "ROLE_PROFESSOR": {
                    return $this->redirectToRoute('teacherExamList');
                    break;
                }
                case "ROLE_ADMIN": {
                    return $this->redirectToRoute('userExamList');
                    break;
                }
            }
        }
        return $this->render('userExamAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$examInfoArray,
            'userExamId' => $userExamId,
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @param Request $request
     * @Route("/deleteUserExam/{userExamId}", name="deleteUserExam")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteUserExam(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $userExamId = $request->attributes->get('userExamId');
        $repo = new UserExamRepository();
        $resultRepository = new ResultRepository();
        $isResult = $resultRepository->getQuantity($userExamId);

        if($isResult !=0 ) {
            $_SESSION['information'][] = array( 'type' => 'error', 'message' => 'The record cannot be deleted, there are links in the database');
        } else {
            $repo->delete($userExamId);
            $_SESSION['information'][] = array( 'type' => 'ok', 'message' => 'Successfully deleted');

        }
        switch ($_SESSION['role']) {
            case "ROLE_PROFESSOR": {
                return $this->redirectToRoute('teacherExamList');
                break;
            }
            case "ROLE_ADMIN": {
                return $this->redirectToRoute('userExamList');
                break;
            }
        }
    }
}
