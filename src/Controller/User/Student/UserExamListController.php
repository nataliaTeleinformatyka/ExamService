<?php

namespace App\Controller\User\Student;

use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\QuestionRepository;
use App\Repository\Admin\ResultRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserExamListController extends AbstractController
{
    /**
     * @Route("studentHomepage", name="studentHomepage")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function studentExamListCreate() {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('examList');
                    break;
                }
            case "ROLE_PROFESSOR":
                {
                    return $this->redirectToRoute('teacherExamList');
                    break;
                }
        }
        $userExamRepository = new UserExamRepository();
        $questionRepository = new QuestionRepository();
        $resultRepository = new ResultRepository();
        $examRepository = new ExamRepository();

        $exist=false;
        $attemptsInfo="";
        $remainingAttempts="";
        $userExamsId = $userExamRepository->getIdUserExams();
        if($userExamsId!=0){
            $userExamsCount = count($userExamsId);
        } else {
            $userExamsCount=0;
        }
        $existExam=false;

         if ($userExamsCount > 0) {
             $info=false;
             $index = 0;

             for ($i = 0; $i < $userExamsCount; $i++) {
                 $userExam = $userExamRepository->getUserExam($userExamsId[$i]);

                 if ($userExam['user_id'] == $_SESSION['user_id']) {
                     $questionsId = $questionRepository->getIdQuestions($userExam['exam_id']);

                     if ($questionsId > 0) {
                         $info = true;
                         $exist = true;
                         $today = new \DateTime();
                         $resultsId = $resultRepository->getIdResults($userExam['user_exam_id']);
                         if ($resultsId != 0) {
                             $numberOfAttemptsInResult = count($resultsId);
                         } else {
                             $numberOfAttemptsInResult = 0;
                         }

                         $examInfo = $examRepository->getExam($userExam['exam_id']);
                         $examName = $examInfo['name'];
                         $maxAttempts = $examInfo['max_attempts'];
                         print_r($userExam['date_of_resolve_exam']['date']);
                         if (date("Y", strtotime($userExam['date_of_resolve_exam']['date'])) < '2020') {
                             if ($maxAttempts != "") {
                                 $remainingAttempts = $maxAttempts - $numberOfAttemptsInResult;
                                 $attemptsInfo = "- Pozostało prób: " . $remainingAttempts;
                             }
                             if ($maxAttempts == "" or $remainingAttempts > 0) {
                                 $startDate = date("Y-m-d", strtotime($examInfo['start_date']['date']));
                                 $endDate = date("Y-m-d", strtotime($examInfo['end_date'] ['date']));
                                 $startYear = date("Y", strtotime($examInfo['start_date']['date']));
                                 $endYear = date("Y", strtotime($examInfo['end_date']['date']));
                                 if ($startYear >= "2020") {
                                     if ($endYear >= "2020") {
                                         $dateInformation = "( " . $startDate . " - " . $endDate . " ) ";
                                     } else {
                                         $dateInformation = "( dostęp od " . $startDate . " ) ";
                                     }
                                 } else {
                                     if ($endYear >= "2020") {
                                         $dateInformation = "( dostęp do " . $endDate . " ) ";
                                     } else {
                                         $dateInformation = " ";
                                     }
                                 }

                                 if (date("Y", strtotime($userExam['date_of_resolve_exam']['date'])) >= "2020") {
                                     $resolveDate = "";
                                 } else {
                                     $resolveDate = date("Y-M-i", strtotime($userExam['date_of_resolve_exam']['date']));
                                 }

                                 $error = '';

                                 $todayDate = $today->format("Y-m-d");
                                 if (($startDate <= $todayDate and $endDate >= $todayDate) or ($startYear < '2020' and $endYear < '2020')
                                     or ($startDate <= $todayDate and $endYear < '2020') or ($startYear < '2020' and $endDate >= $todayDate)) {
                                     $existExam = true;
                                     $tplArray[$index] = array(
                                         'user_exam_id' => $userExam['user_exam_id'],
                                         'user_id' => $userExam['user_id'],
                                         'exam_id' => $userExam['exam_id'],
                                         'date_of_resolve_exam' => $resolveDate,
                                         'access_period' => $dateInformation,
                                         'remaining_attempts' => $attemptsInfo,
                                         'exam_name' => $examName
                                     );
                                     $index++;
                                 } /*else {
                             $info = false;
                             print_r(" NIE ARRAY ");
                             $error = "Brak egzaminów do rozwiązania";
                             $tplArray = array(
                                 'user_id' => '',
                                 'exam_id' => '',
                                 'date_of_resolve_exam' => '',
                                 'start_access_time' => '',
                                 'end_access_time' => ''
                             );
                         }*/
                             }
                         }/* else {
                         $info = false;
                         $error = "Brak egzaminów do rozwiązania";
                         $tplArray = array(
                             'user_id' => '',
                             'exam_id' => '',
                             'date_of_resolve_exam' => '',
                             'start_access_time' => '',
                             'end_access_time' => ''
                         );
                     }*/
                     }
                     } /*else {
                     if($i== $userExamsCount-1 and $exist==false){
                         $info = false;
                         $error = "Brak egzaminów do rozwiązania";
                         $tplArray = array(
                             'user_id' => '',
                             'exam_id' => '',
                             'date_of_resolve_exam' => '',
                             'start_access_time' => '',
                             'end_access_time' => ''
                         );
                     }*/
                 if($existExam==false and $i==$userExamsCount-1){
                     $info = false;
                     $error = "Brak egzaminów do rozwiązania";
                     $tplArray = array(
                         'user_id' => '',
                         'exam_id' => '',
                         'date_of_resolve_exam' => '',
                         'access_period' => '',
                         'remaining_attempts' => '',
                     );
                 }
                 }
             }

        /* else {
             $info = false;
             $error = "Brak egzaminów do rozwiązania";
             $tplArray = array(
                'user_id' => '',
                'exam_id' => '',
                'date_of_resolve_exam' => '',
                'access_period' => '',
                'remaining_attempts' => '',
             );
    }*/

        return $this->render('studentHomepage.html.twig', array(
            'data' => $tplArray,
            'error' => $error,
            'information' => $info
        ));
    }
}