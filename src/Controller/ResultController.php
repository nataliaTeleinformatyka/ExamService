<?php


namespace App\Controller;

use App\Entity\Admin\Result;
use App\Entity\Admin\UserExam;
use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\ExamRepository;
use App\Repository\Admin\QuestionRepository;
use App\Repository\Admin\ResultRepository;
use App\Repository\Admin\UserExamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    /**
     * @Route("studentExam/result", name="result")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $examId = (int)($_SESSION['exam_id']);
        $userId = $_SESSION['user_id'];
        $questionAmount = $_SESSION['questionsAmount'];

        $answerRepo = new AnswerRepository();
        $questionRepo = new QuestionRepository();
        $userExamRepository = new UserExamRepository();
        $points = 0;
        $existQuestion = false;
        //$questionGoodAmount = $questionRepo->getQuantity($examId);
        $amount = 0;
        $questionsId = $questionRepo->getIdQuestions($examId);
        for ($j = 0; $j < $questionAmount; $j++) {
            $isTrueAnswer=false;
            $amount = 0;

            $question = $questionRepo->getQuestion($examId, $_COOKIE['questionId' . $j]);
           // $answersId = $answerRepo->getIdAnswers($examId, $_COOKIE['questionId' . $j]);
            if ( $_COOKIE['amountOfAnswers' . $j]== 0 ) {
                $points++;
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
                    }
                }

                }
            }




        $resultRepository = new ResultRepository();
        $examRepository = new ExamRepository();
        $examInformation = $examRepository->getExam($examId);
        $percentagePassedExam = $examInformation['percentage_passed_exam'];
        print_r($percentagePassedExam);
        $numberOfAttempt = $resultRepository->getIdResults($_COOKIE['user_exam_id']);

        if($points>0){
            if($points/$questionAmount >=$percentagePassedExam){
                $isPassed=true;
                $informationToUser = "Gratulacje, egzamin zakończono pomyślnie.";
                $userExam = new UserExam([]);
                $userExam->setDateOfResolveExam(new \DateTime());
                $information = $userExam->getAllInformation();
                $userExamRepository->update($_COOKIE['user_exam_id'],$information);
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

        return $this->render('studentResult.html.twig', array(
            'points' => $points,
            'information' => $informationToUser

        ));
    }

    /**
     * @Route("resultList/{userExamId}", name="resultList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resultListCreate(Request $request)
    {
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
