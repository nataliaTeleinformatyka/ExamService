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
    public function studentExamStartInfoCreate(Request $request) {
        $examInformation = new UserExamRepository();

        $userExamId = $request->attributes->get('userExamId');

        $userExam = $examInformation->getUserExam($userExamId);

        $exam = new ExamRepository();
        $examInfo = $exam->getExam($userExam['exam_id']);
        $examName = $examInfo['name'];
        $durationOfExam = $examInfo['duration_of_exam'];


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
        $tplArray= array(
            'user_id' => $userExam['user_id'],
            'exam_id' => $userExam['exam_id'],
            'date_of_resolve_exam' => $resolveDate,
            'start_date' => $startDate,
            'end_date' => $endDate
        );

        return $this->render('studentExamStartInfo.html.twig', array(
            'data' => $tplArray,
            'exam_name' => $examName,
            'time' => $durationOfExam,
            'user_exam_id' => $userExamId
        ));
    }
}