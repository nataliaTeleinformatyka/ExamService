<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 13:53
 */

namespace App\Controller\Admin;


use App\Entity\Admin\Question;
use App\Form\Admin\QuestionType;
use App\Repository\Admin\AnswerRepository;
use App\Repository\Admin\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
     /**
     * @Route("question/{examId}", name="question")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $question = new Question([]);
        $repositoryQuestion = new QuestionRepository();

        $examId = $request->attributes->get('examId');
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idExamValue = $request->attributes->get('examId');

            $question = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $question->getAllInformation();
            if($form['file']->getData()==NULL) {
                $filename = "";
            } else {
                $file = $form['file']->getData();
                $filename = $repositoryQuestion->uploadFile($file);
            }

            $repositoryQuestion->insert($idExamValue, $values,$filename);

            return $this->redirectToRoute('questionList', [
                'id' => $examId,
            ]);
        }

        return $this->render('questionAdd.html.twig', [
            'form' => $form->createView(),
            'title' => 'Questions to exam ',
            'idExam' => $examId
        ]);
    }

    /**
     * @Route("questionList/{id}", name="questionList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function questionListCreate(Request $request) {
        $_SESSION['exam_id'] ="";
        $questionInformation= new QuestionRepository();
        $examId = $request->attributes->get('id');

        $idQuestion = $questionInformation -> getQuantity($examId);

        if($idQuestion>0) {
            $info = true;
            for ($i = 0; $i < $idQuestion; $i++) {
                $questions = $questionInformation->getQuestion($examId,$i);


                $tplArray[$i] = array(
                    'id' => $questions['id'],
                    'exam_id' => $questions['exam_id'],
                    'content' => $questions['content'],
                    'max_answers' => $questions['max_answers'],
                    'name_of_file' => $questions['name_of_file'],
                );
            }
        } else {
            $info = false;
            $tplArray = array(
                'id' => '',
                'exam_id' => '',
                'content' => '',
                'max_answers' => '',
                'name_of_file' => '',
            );
        }

    if( isset( $_SESSION['information'] ) && count( $_SESSION['information'] ) > 0  ) {
        $infoDelete = $_SESSION['information'];
    } else {
        $infoDelete = "";
    }
        $_SESSION['information'] = array();

        return $this->render( 'questionList.html.twig', array (
            'data' => $tplArray,
            'examId' => $examId,
            'information' => $info,
            'infoDelete' => $infoDelete
        ) );
    }
    /**
     * @param Request $request
     * @Route("deleteQuestion/{exam}/{question}", name="deleteQuestion")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteQuestion(Request $request)
    {
        $examId = $request->attributes->get('exam');
        $questionId = $request->attributes->get('question');

        $repo = new QuestionRepository();
        $answerRepo = new AnswerRepository();

        $question = $repo->getQuestion($examId,$questionId);
        $filename= $question['name_of_file'];

        $isAnswer = $answerRepo->getQuantity($examId,$questionId);
        if($isAnswer !=0 ){
            $_SESSION['information'][] = array( 'type' => 'error', 'message' => 'The record cannot be deleted, there are links in the database');
        } else {
            $repo->delete($examId, $questionId);
            $repo->deleteFile($filename);
            $_SESSION['information'][] = array( 'type' => 'ok', 'message' => 'Successfully deleted');
        }
        return $this->redirectToRoute('questionList', [
            'id' => $examId,
        ]);
    }

    /**
     * @param Request $request
     * @param Question $question

     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editQuestion/{exam_id}/{id}", name="editQuestion")
     */
    public function editQuestion(Request $request, Question $question)
    {
        $questionInformation = new QuestionRepository();
        $examId = $request->attributes->get('exam_id');
        $questionId = $request->attributes->get('id');

        $_SESSION['exam_id'] = $examId;

        $questions = $questionInformation->getQuestion($examId, $questionId);
        $filenameFromDatabase = $questions['name_of_file'];

        $questionInfoArray = array(
            'content' => $questions['content'],
            'max_answers' => $questions['max_answers'],
            'name_of_file' => $questions['name_of_file'],
        );

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         /*   $exams = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $examValue = $request->attributes->get('id');*/
            $filename = $filenameFromDatabase;

            $values = $question->getAllInformation();
            if($form['file']->getData()== NULL) {
                $filename = $filenameFromDatabase;
            } else {
                $file = $form['file']->getData();
                $filename=$questionInformation->updateFile($file,$filename);
            }

            $questionInformation->update($values,$examId, $questionId,$filename);

            return $this->redirectToRoute('questionList', [
                'id' => $questionId,
            ]);
        }
        return $this->render('questionAdd.html.twig', [
            'form' => $form->createView(),
            'examInformation' =>$questionInfoArray,
            'examId' => $examId
        ]);
    }


    /**
     * @param Request $request
     * @Route("downloadFile/{exam}/{question}", name="downloadFile")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadFile(Request $request)
    {
        $examId = $request->attributes->get('exam');

        $questionId = $request->attributes->get('question');
        $repo = new QuestionRepository();
        $question = $repo->getQuestion($examId,$questionId);
        $filename = $question['name_of_file'];
        $url = "gs://examservicedatabase.appspot.com/" . $filename;
        $file_name = basename($url);


    }
}
