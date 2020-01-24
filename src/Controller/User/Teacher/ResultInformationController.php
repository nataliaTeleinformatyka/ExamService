<?php

namespace App\Controller\User\Teacher;

use App\Repository\Admin\ResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResultInformationController extends AbstractController
{
    /**
     * @Route("teacherResultInfo/{userExam}", name="teacherResultInfo")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resultInfoCreate(Request $request)
    {
        $userExamId = $request->attributes->get('userExam');

        $existResult = false;

        $resultRepository = new ResultRepository();
        $resultsId = $resultRepository->getIdResults($userExamId);
        if($resultsId!=0){
            $resultsCount = count($resultsId);
        } else {
            $resultsCount=0;
        }

        if($resultsCount>0) {
            $existResult=true;
            for ($i = 0; $i < $resultsCount; $i++) {
                $result = $resultRepository->getResult($userExamId,$resultsId[$i]);
                if($result['is_passed']){
                    $isPassed="Tak";
                } else {
                    $isPassed="Nie";
                }
                $resultArray[$i] = array(
                    'id' => $i,
                    'is_passed' => $isPassed,
                    'points' => $result['points'],
                );
            }
        } else {
            $resultArray[] = array(
                'id' => '',
                'is_passed' => '',
                'points' => '',
            );
        }

        return $this->render('teacherResultInfo.html.twig', array(
            'result' => $resultArray,
            'information' => $existResult,
            'exam_id' => $_SESSION['exam_id'],
        ));

    }
}