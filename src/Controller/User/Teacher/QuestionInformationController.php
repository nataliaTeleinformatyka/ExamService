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
        $answerInformation= new AnswerRepository();
        $answerId = $answerInformation -> getQuantity($examId,$questionId);

        if($questions['name_of_file']==""){
            $nameOfFile = "Brak pliku";
        } else {
            $nameOfFile = $questions['name_of_file'];
        }

        if($answerId>0) {
            for ($i = 0; $i < $answerId; $i++) {
                $answers = $answerInformation->getAnswer($examId,$questionId,$i);
                if ($answers['is_true'] == 1) {
                    $is_required = "Tak";
                } else {
                    $is_required = "Nie";
                }
                if ($answers['is_active'] == 1) {
                    $is_required_active = "Tak";
                } else {
                    $is_required_active = "Nie";
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
            'name_of_file' => $nameOfFile,
            'answer_data' => $answerArray
        ));

    }
}