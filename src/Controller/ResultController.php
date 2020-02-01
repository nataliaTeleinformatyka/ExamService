<?php

namespace App\Controller;

use App\Entity\Admin\Result;
use App\Entity\Admin\UserExam;
use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\ResultRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    /**
     * @Route("studentExam/result", name="result")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new() {
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

        $examId = (int)($_SESSION['exam_id']);
        $questionAmount = $_SESSION['questionsAmount'];

        $answerRepo = new AnswerRepository();
        $userExamRepository = new UserExamRepository();
        $points = 0;

        for ($j = 0; $j < $questionAmount; $j++) {
            $isTrueAnswer=false;

            if ( $_COOKIE['amountOfAnswers' . $j]== 0 ) {
                $points++;
                print_r("ZEROOO");
            } else {
                $answerIds = json_decode($_COOKIE['allAnswers' . $j]);
                $amount = 0;
                for ($m = 0; $m < count($answerIds); $m++) {
                    $userAnswerInformation = $answerRepo->getAnswer($examId, $_COOKIE['questionId' . $j], $answerIds[$m]);
                    if ($userAnswerInformation['is_true'] == true) {
                        $trueAnswers[$amount] = $userAnswerInformation['id'];
                        $amount++;
                    }
                }
                if ($_COOKIE['userAnswerAmount' . $j] == $amount){
                    for ($t = 0; $t < $amount; $t++) {
                        if (isset($_COOKIE['userAnswer' . $j . $t])){
                            $userAnswerInformation = $answerRepo->getAnswer($examId, $_COOKIE['questionId' . $j], $_COOKIE['userAnswer' . $j . $t]);
                            if ($userAnswerInformation['id'] == $trueAnswers[$t]) {
                                $isTrueAnswer = true;
                            }
                            if ($t == $amount - 1 and $isTrueAnswer == true) {
                                $points++;
                            }
                        }
                        setcookie ('userAnswer'.$j.$t, "", time() - 3600);
                    }
                }
            }

            for($k=0;$k<$_COOKIE['amountOfAnswers'.$j];$k++){
                setcookie ('answerId'.$j.$k, "", time() - 3600);
                setcookie ('answerContent'.$j.$k, "", time() - 3600);
            }
            setcookie ('amountOfAnswers'.$j, "", time() - 3600);
            setcookie ('userAnswerAmount'.$j, "", time() - 3600);
            setcookie ('questionId'.$j, "", time() - 3600);
            setcookie ('allAnswers'.$j, "", time() - 3600);
            setcookie ('questionContent'.$j, "", time() - 3600);
            setcookie ('questionId'.$j, "", time() - 3600);
            setcookie ('questionMaxAnswers'.$j, "", time() - 3600);
        }

        $resultRepository = new ResultRepository();
        $examRepository = new ExamRepository();

        $examInformation = $examRepository->getExam($examId);
        $percentagePassedExam = $examInformation['percentage_passed_exam'];
        $numberOfAttempt = $resultRepository->getIdResults($_COOKIE['user_exam_id']);

        if($points>0) {
            print_r(" podloga ".($points/$questionAmount). " aby zdac ".$percentagePassedExam );
            if(($points/$questionAmount)*100 >=$percentagePassedExam){
                $isPassed=true;
                $informationToUser = "Gratulacje, egzamin zakończono pomyślnie.";
                $userExam = new UserExam([]);
                print_r(new \DateTime("now"));
                $userExam->setDateOfResolveExam(new \DateTime("now"));
                $information = $userExam->getAllInformation();
                $userExamRepository->update($information,$_COOKIE['user_exam_id']);
            } else {
                $isPassed = false;
                $informationToUser = "Niestety nie udało się zakończyć egzaminu z wynikiem pozytywnym.";
            }
        } else {
            $isPassed=false;
            $informationToUser = "Niestety nie udało się zakończyć egzaminu z wynikiem pozytywnym.";
        }

        $result = new Result([]);
        $result->setPoints($points);
        $result->setNumberOfAttempt($numberOfAttempt);
        $result->setIsPassed($isPassed);

        $data = $result->getAllInformation();
        $resultRepository->insert($_COOKIE['user_exam_id'],$data);

        $_SESSION['questionsAmount']="";
        setcookie ('accessTime', "", time() - 3600);
        setcookie ('user_exam_id', "", time() - 3600);
        setcookie ('questionAmount', "", time() - 3600);

        return $this->render('studentResult.html.twig', array(
            'points' => $points,
            'information' => $informationToUser
        ));
    }

    /**
     * @Route("resultList/{userExamId}", name="resultList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resultListCreate(Request $request) {
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

        $userExamId=(int)$request->attributes->get('userExamId');

        $resultInformation = new ResultRepository();
        $id = $resultInformation->getIdResults($userExamId);
        if($id!=0){
            $idCount = count($id);
        } else {
            $idCount=0;
        }
        $isPassed="False";
        if ($idCount > 0) {
            $info = true;
            for ($i = 0; $i < $idCount; $i++) {
                $results = $resultInformation->getResult($userExamId,$id[$i]);
                if($results['is_passed'])
                    $isPassed="True";

                $tplArray[$i] = array(
                    'id' => $results['id'],
                    'points' => $results['points'],
                    'is_passed' => $isPassed,
                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'id' => '',
                'points' => '',
                'is_passed' => '',
            );
        }

        return $this->render('resultList.html.twig', array(
            'data' => $tplArray,
            'information' => $info,

        ));
    }
}
