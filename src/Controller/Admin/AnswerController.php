<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Answer;
use App\Form\Admin\AnswerType;
use App\Repository\Admin\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController {

    /**
     * @Route("answer/{examId}/{questionId}", name="answer")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $question = new Answer([]);
        $examId = $request->attributes->get('examId');
        $questionId = $request->attributes->get('questionId');

        $form = $this->createForm(AnswerType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data[0] = $request->request->get('content');
            $data[1] = $request->request->get('is_true');

            $answer = $form->getData();

            $values = $answer->getAllInformation();
            $examValue = $request->attributes->get('examId');
            $questionValue = $request->attributes->get('questionId');

            $repositoryAnswer = new AnswerRepository();
            $repositoryAnswer->insert($examValue, $questionValue, $values);

            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('answerList', [
                            'examId' => $examId,
                            'questionId' => $questionId
                        ]);
                        break;
                    }
                case "ROLE_PROFESSOR":
                    {
                        return $this->redirectToRoute('teacherQuestionInfo', [
                            'exam' => $examId,
                            'question' => $questionId
                        ]);
                        break;
                    }
            }
        }
        return $this->render('answerAdd.html.twig', [
            'form' => $form->createView(),
            'title' => 'Answers to question ',
            'examId' => $examId,
            'questionId' => $questionId,
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @Route("answerList/{questionId}/{examId}", name="answerList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function answerListCreate(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        switch ($_SESSION['role']) {
            case "ROLE_PROFESSOR": {
                return $this->redirectToRoute('teacherExamList');
                break;
            }
            case "ROLE_STUDENT": {
                return $this->redirectToRoute('studentHomepage');
                break;
            }
        }

        $answerInformation= new AnswerRepository();
        $_SESSION['exam_id'] = "";
        $_SESSION['question_id'] = "";

        $examId = $request->attributes->get('examId');
        $questionId = $request->attributes->get('questionId');

        $answersId = $answerInformation->getIdAnswers($examId,$questionId);
        if($answersId!=0){
            $answersCount = count($answersId);
        } else {
            $answersCount=0;
        }

        if($answersCount>0) {
            $info=true;
            for ($i = 0; $i < $answersCount; $i++) {
                $answers = $answerInformation->getAnswer($examId,$questionId,$answersId[$i]);

                if ($answers['is_true'] == 1) {
                    $is_required = "true";
                } else {
                    $is_required = "false";
                }
                $tplArray[$i] = array(
                    'id' => $i,
                    'content' => $answers['content'],
                    'is_true' => $is_required,
                );
            }
        } else {
            $info=false;
            $tplArray = array(
                'id' => 0,
                'content' => 0,
                'is_true' => 0,
            );
        }
        return $this->render( 'answerList.html.twig', array (
            'data' => $tplArray,
            'examId' => $examId,
            'questionId' => $questionId,
            'information' => $info
        ) );
    }
    /**
     * @param Request $request
     * @param Answer $answer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editAnswer/{examId}/{questionId}/{id}", name="editAnswer")
     */
    public function editAnswer(Request $request, Answer $answer) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $examId = (int)$request->attributes->get('examId');
        $questionId = (int)$request->attributes->get('questionId');
        $id = (int)$request->attributes->get('id');

        $_SESSION['exam_id'] = $examId;
        $_SESSION['question_id'] = $questionId;

        $answerInformation = new AnswerRepository();
        $answers = $answerInformation->getAnswer($examId,$questionId,$id);

        $examInfoArray = array(
            'content' => $answers['content'],
            'is_true' => $answers['is_true'],
        );

        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $values = $answer->getAllInformation();

            $answerInformation->update($values,$examId,$questionId,$id);
            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('answerList', [
                            'examId' => $examId,
                            'questionId' => $questionId
                        ]);
                        break;
                    }
                case "ROLE_PROFESSOR":
                    {
                        return $this->redirectToRoute('teacherQuestionInfo', [
                            'exam' => $examId,
                            'question' => $questionId
                        ]);
                        break;
                    }
            }

        }
        return $this->render('answerAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$examInfoArray,
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("deleteAnswer/{exam}/{question}/{answer}", name="deleteAnswer")
     */
    public function deleteAnswer(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $examId = $request->attributes->get('exam');
        $questionId = $request->attributes->get('question');
        $answerId = $request->attributes->get('answer');
        $repo = new AnswerRepository();

        $repo->delete($examId,$questionId, $answerId);

        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('answerList', [
                        'examId' => $examId,
                        'questionId' => $questionId
                    ]);
                    break;
                }
            case "ROLE_PROFESSOR":
                {
                    return $this->redirectToRoute('teacherQuestionInfo', [
                        'exam' => $examId,
                        'question' => $questionId
                    ]);
                    break;
                }
        }
    }
}
