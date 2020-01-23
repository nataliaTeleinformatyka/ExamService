<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 12.12.2019
 * Time: 20:40
 */

namespace App\Controller\User\Student;


use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\QuestionRepository;
use App\Repository\Admin\ResultRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\Annotation\Route;

class UserExamListController extends AbstractController
{
    /**
     * @Route("studentHomepage", name="studentHomepage")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    //todo: dostep do egzaminow user_id = id aktualnego usera, przedzial dostepu, ilosc pozostalych prob
    public function studentExamListCreate() {
        $userExamRepository = new UserExamRepository();
        $questionRepository = new QuestionRepository();
        $resultRepository = new ResultRepository();
        $examRepository = new ExamRepository();

        $exist=false;
        $userExamsId = $userExamRepository->getIdUserExams();
        if($userExamsId!=0){
            $userExamsCount = count($userExamsId);
        } else {
            $userExamsCount=0;
        }

         if ($userExamsCount > 0) {
             $info=false;
             $index = 0;

             for ($i = 0; $i < $userExamsCount; $i++) {
                 $userExam = $userExamRepository->getUserExam($userExamsId[$i]);

                 if ($userExam['user_id'] == $_SESSION['user_id']) {
                     $questionsId = $questionRepository->getIdQuestions($userExam['exam_id']);

                     if ($questionsId > 0) {
                         $info = true;
                         $exist=true;
                         $today=new \DateTime();
                        // $todayDate = date("Y", strtotime($today));

                         $numberOfAttemptsInResult = $resultRepository->getQuantityAttempt($userExam['exam_id'], $_SESSION['user_id']);
                         $examInfo = $examRepository->getExam($userExam['exam_id']);
                         $examName = $examInfo['name'];
                         $maxAttempts = $examInfo['max_attempts'];
                         if ($maxAttempts != NULL) {
                             $remainingAttempts = $maxAttempts - $numberOfAttemptsInResult;
                             $attemptsInfo = "- Pozostało prób: " . $remainingAttempts;
                         }
                         $startDate = date("Y-m-d", strtotime($examInfo['start_date']['date']));
                         $endDate = date("Y-m-d", strtotime($examInfo['end_date'] ['date']));
                            $startYear = date("Y", strtotime($examInfo['start_date']['date']));
                            $endYear = date("Y", strtotime($examInfo['end_date']['date']));
                         if ( $startYear >= "2020") {
                             //$startDate = date("Y-m-d", strtotime($examInfo['start_date']['date']));
                             if ( $endYear >= "2020") {
                               //  $endDate = date("Y-m-d", strtotime($examInfo['end_date']['date']));
                                 $dateInformation = "( " . $startDate . " - " . $endDate . " ) ";
                             } else {
                                 $dateInformation = "( dostęp od " . $startDate . " ) ";
                             }
                         } else {
                             if ($endYear >= "2020") {
                                // $endDate = date("Y-m-d", strtotime($examInfo['end_date'] ['date']));
                                 $dateInformation = "( dostęp do " . $endDate . " ) ";
                             } else {
                                 $dateInformation = " ";
                             }
                         }

                         //if($startDate!="")

                         if (date("Y", strtotime($userExam['date_of_resolve_exam']['date'])) >= "2020") {
                             $resolveDate = "";
                         } else {
                             $resolveDate = date("Y-M-i", strtotime($userExam['date_of_resolve_exam']['date']));
                         }

                         $error = '';
                       //  print_r(" startdate ".$startDate." enddate ".$endDate);

                        $todayDate = $today->format("Y-m-d");

                         if(($startDate<=$todayDate and $endDate>=$todayDate) or ($startYear<'2020' and $endYear<'2020')
                             or ($startDate<=$todayDate and $endYear<'2020') or ($startYear<'2020' and $endDate>=$todayDate)){
                             print_r(" TTUTAJ ");
                             $tplArray[$i] = array(
                                 'user_exam_id' => $userExam['user_exam_id'],
                                 'user_id' => $userExam['user_id'],
                                 'exam_id' => $userExam['exam_id'],
                                 'date_of_resolve_exam' => $resolveDate,
                                 'access_period' => $dateInformation,
                                 'remaining_attempts' => $attemptsInfo,
                                 'exam_name' => $examName
                                 //todo class result and dodac tutaj ilosc pozostalych prob
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
                     } else {
                         print_r( " BUBUBU ");
                         $info = false;
                         $error = "Brak egzaminów do rozwiązania";
                         $tplArray = array(
                             'user_id' => '',
                             'exam_id' => '',
                             'date_of_resolve_exam' => '',
                             'start_access_time' => '',
                             'end_access_time' => ''
                         );
                     }
                 } else {
                     if($i== $userExamsCount-1 and $exist==false){
                         print_r("TITAJ");
                         $info = false;
                         $error = "Brak egzaminów do rozwiązania";
                         $tplArray = array(
                             'user_id' => '',
                             'exam_id' => '',
                             'date_of_resolve_exam' => '',
                             'start_access_time' => '',
                             'end_access_time' => ''
                         );
                     }
                 }
             }
         }
         else {
             print_r("JESTEM TU END ");
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
    print_r($info);
        return $this->render('studentHomepage.html.twig', array(
        'data' => $tplArray,
        'error' => $error,
        'information' => $info
    ));
    }

    public function compareDate($startDate,$endDate){
    }
}