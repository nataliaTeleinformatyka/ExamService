<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Question;
use App\Form\Admin\QuestionType;
use App\Repository\Admin\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController {

     /**
     * @Route("question/{examId}", name="question")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $question = new Question([]);
        $repositoryQuestion = new QuestionRepository();
        $examId = $request->attributes->get('examId');

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $idExamValue = $request->attributes->get('examId');
            $question = $form->getData();
            $values = $question->getAllInformation();

            $repositoryQuestion->insert($idExamValue, $values);

            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('questionList', [
                            'id' => $examId,
                        ]);
                        break;
                    }
                case "ROLE_PROFESSOR":
                    {
                        return $this->redirectToRoute('teacherExamInfo', [
                            'exam' => $examId,
                        ]);
                        break;
                    }
            }
        }
        return $this->render('questionAdd.html.twig', [
            'form' => $form->createView(),
            'title' => 'Questions to exam ',
            'idExam' => $examId,
            'role' => $_SESSION['role'],
        ]);
    }

    /**
     * @Route("questionList/{id}", name="questionList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function questionListCreate(Request $request) {
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

        $_SESSION['exam_id'] ="";
        $examId = $request->attributes->get('id');

        $questionRepository= new QuestionRepository();
        $questionsId =$questionRepository->getIdQuestions($examId);

        if($questionsId!=0){
            $questionAmount = count($questionsId);
        } else {
            $questionAmount=0;
        }

        if($questionAmount>0) {
            $info = true;
            for($j=0;$j<$questionAmount;$j++){
                $questions = $questionRepository->getQuestion($examId,$questionsId[$j]);
                $tplArray[$j] = array(
                    'id' => $questions['id'],
                    'exam_id' => $questions['exam_id'],
                    'content' => $questions['content'],
                    'max_answers' => $questions['max_answers'],
                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'id' => '',
                'exam_id' => '',
                'content' => '',
                'max_answers' => '',
            );
        }

        return $this->render( 'questionList.html.twig', array (
            'data' => $tplArray,
            'examId' => $examId,
            'information' => $info,
        ));
    }

    /**
     * @param Request $request
     * @Route("deleteQuestion/{exam}/{question}", name="deleteQuestion")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteQuestion(Request $request) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $examId = $request->attributes->get('exam');
        $questionId = $request->attributes->get('question');

        $repository = new QuestionRepository();
        $repository->delete($examId,$questionId);

        switch ($_SESSION['role']) {
            case "ROLE_ADMIN":
                {
                    return $this->redirectToRoute('questionList', [
                        'id' => $examId,
                    ]);
                    break;
                }
            case "ROLE_PROFESSOR":
                {
                    return $this->redirectToRoute('teacherExamInfo', [
                        'exam' => $examId,
                    ]);
                    break;
                }
        }
    }

    /**
     * @param Request $request
     * @param Question $question

     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editQuestion/{exam_id}/{id}", name="editQuestion")
     */
    public function editQuestion(Request $request, Question $question) {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");
        if($_SESSION['role']=="ROLE_STUDENT")
            $this->redirectToRoute('studentHomepage');

        $questionInformation = new QuestionRepository();
        $examId = $request->attributes->get('exam_id');
        $questionId = $request->attributes->get('id');

        $_SESSION['exam_id'] = $examId;
        $questions = $questionInformation->getQuestion($examId, $questionId);

        $questionInfoArray = array(
            'content' => $questions['content'],
            'max_answers' => $questions['max_answers'],
        );

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $values = $question->getAllInformation();

            $questionInformation->update($values,$examId, $questionId);

            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('questionList', [
                            'id' => $examId,
                        ]);
                        break;
                    }
                case "ROLE_PROFESSOR":
                    {
                        return $this->redirectToRoute('teacherExamInfo', [
                            'exam' => $examId,
                        ]);
                        break;
                    }
            }
        }
        return $this->render('questionAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$questionInfoArray,
            'examId' => $examId,
            'role' => $_SESSION['role'],
        ]);
    }
}
