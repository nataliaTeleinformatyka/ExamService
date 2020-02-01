<?php

namespace App\Controller\User\Teacher;

use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuestionInformationController extends AbstractController
{
    /**
     * @Route("teacherQuestionInfo/{exam}/{question}", name="teacherQuestionInfo")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function questionInfoCreate(Request $request) {
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

        $examId = $request->attributes->get('exam');
        $questionId = $request->attributes->get('question');
        $existAnswer = false;
        $_SESSION['question_id'] = $questionId;
        $questionInformation= new QuestionRepository();
        $questions = $questionInformation->getQuestion($examId,$questionId);
        $content = $questions['content'];
        $maxAnswers = $questions['max_answers'];
        $answerInformation= new AnswerRepository();

        $answersId = $answerInformation-> getIdAnswers($examId,$questionId);
        if($answersId!=0){
            $answersCount = count($answersId);
        } else {
            $answersCount=0;
        }

        if($answersCount>0) {
            $existAnswer=true;
            for ($i = 0; $i < $answersCount; $i++) {
                $answers = $answerInformation->getAnswer($examId,$questionId,$answersId[$i]);
                if ($answers['is_true'] == 1) {
                    $is_required = "Tak";
                } else {
                    $is_required = "Nie";
                }

                $answerArray[$i] = array(
                    'id' => $i,
                    'content' => $answers['content'],
                    'is_true' => $is_required,
                );
            }
        } else {
            $answerArray = array(
                'id' => '',
                'content' => '',
                'is_true' => '',
            );
        }

        return $this->render('teacherQuestionInfo.html.twig', array(
            'question_id' => $questionId,
            'exam_id' => $examId,
            'content' => $content,
            'max_answers' => $maxAnswers,
            'answer_data' => $answerArray,
            'information' => $existAnswer
        ));

    }
}