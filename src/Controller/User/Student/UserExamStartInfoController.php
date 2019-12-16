<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 13.12.2019
 * Time: 11:02
 */

namespace App\Controller\User\Student;


use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserExamStartInfoController extends AbstractController
{
    /**
     * @Route("studentExamStartInfo/{userExamId}", name="studentExamStartInfo")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    //todo: dostep do egzaminow user_id = id aktualnego usera, przedzial dostepu, ilosc pozostalych prob
    public function studentExamStartInfoCreate(Request $request) {
        $examInformation = new UserExamRepository();

        $userExamId = $request->attributes->get('userExamId');

        $userExam = $examInformation->getUserExam($userExamId);

        $exam = new ExamRepository();
        $examInfo = $exam->getExam($userExam['exam_id']);
        $examName = $examInfo['name'];
        $durationOfExam = $examInfo['duration_of_exam'];
        $time = date("H",strtotime($durationOfExam['date']))*60 + date("i",strtotime($durationOfExam['date']));


        if ($userExam['start_access_time'] == "NULL") {
            $startDate = " ";
        } else {
            $startDate = $userExam['start_access_time'];
        }
        print_r($startDate);
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
        $tplArray= array(
            'user_id' => $userExam['user_id'],
            'exam_id' => $userExam['exam_id'],
            'date_of_resolve_exam' => $resolveDate,
            'start_date' => $startDate,
            'end_date' => $endDate
            //todo class result and dodac tutaj ilosc pozostalych prob
        );

        return $this->render('studentExamStartInfo.html.twig', array(
            'data' => $tplArray,
            'exam_name' => $examName,
            'time' => $time,
            'user_exam_id' => $userExamId
        ));
    }
}