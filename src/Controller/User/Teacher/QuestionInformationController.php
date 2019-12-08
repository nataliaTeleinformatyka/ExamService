<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 08.12.2019
 * Time: 19:26
 */

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
    public function questionInfoCreate(Request $request)
    {
        $examId = $request->attributes->get('exam');
        $questionId = $request->attributes->get('question');

        $questionInformation= new QuestionRepository();
        $questions = $questionInformation->getQuestion($examId,$questionId);
        if ($questions['is_multichoice'] == 1) {
            $is_required = "true";
        } else {
            $is_required = "false";
        }
        if ($questions['is_file'] == 1) {
            $is_required_file = "true";
        } else {
            $is_required_file = "false";
        }

        $answerInformation= new AnswerRepository();
        $answerId = $answerInformation -> getQuantity($examId,$questionId);

        if($answerId>0) {
            for ($i = 0; $i < $answerId; $i++) {
                $answers = $answerInformation->getAnswer($examId,$questionId,$i);
                if ($answers['is_true'] == 1) {
                    $is_required = "true";
                } else {
                    $is_required = "false";
                }
                if ($answers['is_active'] == 1) {
                    $is_required_active = "true";
                } else {
                    $is_required_active = "false";
                }

                $answerArray[$i] = array(
                    'id' => $i,
                    'content' => $answers['content'],
                    'is_true' => $is_required,
                    'is_active' => $is_required_active
                );
            }
        } else {
            $answerArray = array(
                'id' => 0,
                'content' => 0,
                'is_true' => 0,
                'is_active' => 0
            );
        }


        return $this->render('teacherQuestionInfo.html.twig', array(
            'question_id' => $questionId,
            'exam_id' => $questions['exam_id'],
            'content' => $questions['content'],
            'max_answers' => $questions['max_answers'],
            'is_multichoice' => $is_required,
            'is_file' => $is_required_file,
            'answer_data' => $answerArray
        ));

    }
}