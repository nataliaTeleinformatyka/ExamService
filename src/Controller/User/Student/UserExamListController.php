<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 12.12.2019
 * Time: 20:40
 */

namespace App\Controller\User\Student;


use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserExamListController extends AbstractController
{
    /**
     * @Route("studentHomepage", name="studentHomepage")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    //todo: dostep do egzaminow user_id = id aktualnego usera, przedzial dostepu, ilosc pozostalych prob
    public function studentExamListCreate() {
         $examInformation = new UserExamRepository();
         $id = $examInformation->getQuantity();
         if ($id > 0) {
             $info = true;
             $index = 0;
             for ($i = 0; $i < $id; $i++) {
                 $userExam = $examInformation->getUserExam($i);
                 //print_r($userExam);
                // print_r($userExam['user_id']);
                 print_r($_SESSION['user_id']);
                 if ($userExam['user_id'] == $_SESSION['user_id']) {
                     print_r("jestem tutaj");

                     $exam = new ExamRepository();
                     $examInfo = $exam->getExam($userExam['exam_id']);
                     $examName = $examInfo['name'];
                    print_r($examInfo);

                     if ($userExam['start_access_time'] == "NULL") {
                         $startDate = " ";
                     } else {
                         $startDate = $userExam['start_access_time'];
                     }
                     if ($userExam['end_access_time'] == "NULL") {
                         $endDate = " ";
                     } else {
                         $endDate = $userExam['end_access_time'];
                     }
                     if ($userExam['date_of_resolve_exam'] == "NULL") {
                         $resolveDate = " ";
                     } else {
                         $resolveDate = $userExam['date_of_resolve_exam'];
                     }
                     $tplArray[$i] = array(
                         'user_exam_id' => $userExam['user_exam_id'],
                         'user_id' => $userExam['user_id'],
                         'exam_id' => $userExam['exam_id'],
                         'date_of_resolve_exam' => $resolveDate,
                         'access_period' => "(".$startDate."-".$endDate.")",
                         'exam_name' => $examName
                         //todo class result and dodac tutaj ilosc pozostalych prob
                     );
                     $index++;
                 }  /*else {
                     print_r("a teraz tttt ");
                     $info = false;
                     $tplArray[$i] = array(
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
             $tplArray = array(
            'user_id' => '',
            'exam_id' => '',
            'date_of_resolve_exam' => '',
            'start_access_time' => '',
            'end_access_time' => ''
        );
    }
    print_r($info);
        return $this->render('studentHomepage.html.twig', array(
        'data' => $tplArray,
        'information' => $info
    ));
    }
}