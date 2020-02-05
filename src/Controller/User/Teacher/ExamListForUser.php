<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 04.02.2020
 * Time: 13:25
 */

namespace App\Controller\User\Teacher;

use App\Repository\Admin\ResultRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Admin\ExamRepository;

class ExamListForUser extends AbstractController
{
    /**
     * @Route("teacherExamListForUser/{userId}", name="teacherExamListForUser")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function examListForUser(Request $request){
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('examList');
                    break;
                }
            case "ROLE_STUDENT":
                {
                    return $this->redirectToRoute('studentHomepage');
                    break;
                }
        }
        $amount =0;
        $userExamAmount=0;
        $info = false;
        $resultArray=[];

        $userId = $request->attributes->get('userId');
        $userExamRepository = new UserExamRepository();
        $examRepository = new ExamRepository();
        $resultRepository = new ResultRepository();

        $userExamsId = $userExamRepository->getUserExamIdForUser($userId);

        if($userExamsId == "") {
            $information = " Brak przypisanych egzaminów ";
        } else {
            $information = "";
            $userExamAmount = count($userExamsId);
        }
        for($i=0;$i<$userExamAmount;$i++) {
            $userExamInformation = $userExamRepository->getUserExam($userExamsId[$i]);
            $examInformation = $examRepository->getExam($userExamInformation['exam_id']);
            if($examInformation['created_by'] == $_SESSION['user_id'] || $examInformation['created_by']=="-1"){
                $info = true;
                $tplArray[$amount] = array (
                    'id' => $examInformation['exam_id'],
                    'name' => $examInformation['name'],
                );
                $resultsId = $resultRepository->getIdResults($userExamsId[$i]);

                if($resultsId==0){
                    $resultsAmount = 0;
                } else
                    $resultsAmount = count($resultsId);
                for($j=0;$j<$resultsAmount;$j++){
                    $resultInformation = $resultRepository->getResult($userExamsId[$i],$resultsId[$j]);
                    if($resultInformation['is_passed']) {
                        $isPassed = "Tak";
                    } else
                        $isPassed = "Nie";
                    $resultArray[$j] = array(
                        'exam_name' => $examInformation['name'],
                        'is_passed' => $isPassed,
                        'points' => $resultInformation['points']
                    );
                }

            }
            if($info==false && $i<($userExamAmount-1)) {
                $tplArray[] = array(
                'id' => '',
                'name' => ''
                );
                $resultArray[] = array(
                    'exam_name' => '',
                    'is_passed' => '',
                    'points' => ''
                );
            }
        }




        return $this->render('teacherExamListForUser.html.twig', array (
            'information' => $information,
            'data' => $tplArray,
            'result_data' => $resultArray,
            'info' => $info
        ));

    }
}