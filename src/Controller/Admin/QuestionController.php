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
use App\Repository\Admin\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
     /**
     * @Route("question/{idExam}", name="question")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $question = new Question([]);
        $idExam = $request->attributes->get('idExam');
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data[0] = $request->request->get('content');
            $data[1] = $request->request->get('max_answers');
            $data[2] = $request->request->get('is_multichoice');
            $data[3] = $request->request->get('is_file');

            $idExamValue = $request->attributes->get('idExam');

            $question = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $question->getAllInformation();

            $repositoryExam = new QuestionRepository();
            $repositoryExam->insert($idExamValue, $values);

            return $this->redirectToRoute('questionList', [
                'id' => $idExam,
            ]);
        }

        return $this->render('questionAdd.html.twig', [
            'form' => $form->createView(),
            'title' => 'Questions to exam ',
            'idExam' => $idExam
        ]);
    }

    /**
     * @Route("questionList/{id}", name="questionList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function questionListCreate(Request $request) {
        $questionInformation= new QuestionRepository();
        $idExam = $request->attributes->get('id');

        $idQuestion = $questionInformation -> getQuantity($idExam);

        if($idQuestion>0) {
            for ($i = 0; $i < $idQuestion; $i++) {
                $questions = $questionInformation->getQuestion($idExam,$i);
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

                $tplArray[$i] = array(
                    'id' => $i,
                    'id_exam' => $questions['id_exam'],
                    'content' => $questions['content'],
                    'max_answers' => $questions['max_answers'],
                    'is_multichoice' => $is_required,
                    'is_file' => $is_required_file
                );
            }
        } else {
            $tplArray = array(
                'id' => 0,
                'id_exam' => 0,
                'content' => 0,
                'max_answers' => 0,
                'is_multichoice' => 0,
                'is_file' => 0
            );
        }
        return $this->render( 'questionList.html.twig', array (
            'data' => $tplArray,
            'idExam' => $idExam
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

        $repo->delete($examId, $questionId);
        //todo: redirect to questionList nie usuwa zapytania ktore jest jako 1
        //todo: zapytanie czy chce usunac  gdy sa powiazane answers
        return $this->redirectToRoute('questionList', [
            'id' => $examId,
        ]);
    }
    //todo: edit question
}
