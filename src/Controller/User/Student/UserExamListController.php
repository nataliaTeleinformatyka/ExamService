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
         $answerRepository = new AnswerRepository();
         $resultRepository = new ResultRepository();

         $userExamsId = $userExamRepository->getIdUserExams();
        if($userExamsId!=0){
            $userExamsCount = count($userExamsId);
        } else {
            $userExamsCount=0;
        }
         if ($userExamsCount > 0) {
             $info=false;
             $index = 0;
             print_r(" TUTAJJESTEM ");
             for ($i = 0; $i < $userExamsCount; $i++) {
                 $userExam = $userExamRepository->getUserExam($userExamsId[$i]);
                 if ($userExam['user_id'] == $_SESSION['user_id']) {
                     print_r("TUUU".$userExam['exam_id']);
                     $questionsId = $questionRepository->getIdQuestions($userExam['exam_id']);
                     if ($questionsId > 0) {
print_r("a teraz tutaj ");
                         $info = true;
                         $numberOfAttemptsInResult = $resultRepository->getQuantityAttempt($userExam['exam_id'], $_SESSION['user_id']);

                         $exam = new ExamRepository();
                         $examInfo = $exam->getExam($userExam['exam_id']);
                         $examName = $examInfo['name'];
                         $maxAttempts = $examInfo['max_attempts'];
                         if ($maxAttempts != NULL) {
                             $remainingAttempts = $maxAttempts - $numberOfAttemptsInResult;
                             $attemptsInfo = "- Pozostało prób: " . $remainingAttempts;
                         }

                         if ($userExam['start_access_time'] != "NULL") {
                             $startDate = $userExam['start_access_time'];
                             if ($userExam['end_access_time'] != "NULL") {
                                 $endDate = $userExam['end_access_time'];
                                 $dateInformation = "( " . $startDate . " - " . $endDate . " ) ";
                             } else {
                                 $dateInformation = "( dostęp od " . $startDate . " ) ";
                             }
                         } else {
                             if ($userExam['end_access_time'] != "NULL") {
                                 $endDate = $userExam['end_access_time'];
                                 $dateInformation = "( dostęp do" . $endDate . " ) ";
                             } else {
                                 $dateInformation = " ";
                             }
                         }

                         if ($userExam['date_of_resolve_exam'] == "NULL") {
                             $resolveDate = " ";
                         } else {
                             $resolveDate = $userExam['date_of_resolve_exam'];
                         }
                         $error = '';
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
                         /*} else {
                         return $this->redirectToRoute('login');*/
                     } else {
                         $info = false;
                         $error = '';
                         $tplArray = array(
                             'user_id' => '',
                             'exam_id' => '',
                             'date_of_resolve_exam' => '',
                             'start_access_time' => '',
                             'end_access_time' => ''
                         );
                     }
                 } /*else {
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
         }
         else {
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
}