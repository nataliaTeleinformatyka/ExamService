<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 15:51
 */

namespace App\Controller\Admin;


use App\Entity\Admin\Answer;
use App\Form\Admin\AnswerType;
use App\Repository\Admin\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController
{
    /**
     * @Route("answer/{examId}/{questionId}", name="answer")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Answer::class);
        $question = new Answer([]);

        $examId = $request->attributes->get('examId');
        $questionId = $request->attributes->get('questionId');

        $form = $this->createForm(AnswerType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data[0] = $request->request->get('content');
            $data[1] = $request->request->get('is_true');
            $data[2] = $request->request->get('is_active');

            $answer = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $answer->getAllInformation();
            $examValue = $request->attributes->get('examId');
            $questionValue = $request->attributes->get('questionId');

            $repositoryAnswer = new AnswerRepository();
            $repositoryAnswer->insert($examValue, $questionValue, $values);

            return $this->redirectToRoute('answerList', [
                'examId' => $examId,
                'questionId' => $questionId
            ]);
        }

        return $this->render('answerAdd.html.twig', [
            'form' => $form->createView(),
            'title' => 'Answers to question ',
            'examId' => $examId,
            'questionId' => $questionId
        ]);
    }

    /**
     * @Route("answerList/{questionId}/{examId}", name="answerList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function answerListCreate(Request $request) {
        $answerInformation= new AnswerRepository();

        $examId = $request->attributes->get('examId');
        $questionId = $request->attributes->get('questionId');
        $id = $answerInformation -> getQuantity($examId,$questionId);

        if($id>0) {
            for ($i = 0; $i < $id; $i++) {
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

                $tplArray[$i] = array(
                    'id' => $i,
                    'content' => $answers['content'],
                    'is_true' => $is_required,
                    'is_active' => $is_required_active
                );
            }
        } else {
            $tplArray = array(
                'id' => 0,
                'content' => 0,
                'is_true' => 0,
                'is_active' => 0
            );
        }
        return $this->render( 'answerList.html.twig', array (
            'data' => $tplArray,
            'examId' => $examId,
            'questionId' => $questionId
        ) );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("deleteAnswer/{exam}/{question}/{answer}", name="deleteAnswer")
     */
    public function deleteAnswer(Request $request)
    {
        $examId = $request->attributes->get('exam');
        $questionId = $request->attributes->get('question');
        $answerId = $request->attributes->get('answer');
        $repo = new AnswerRepository();

        $repo->delete($examId,$questionId, $answerId);

        //todo: redirect to answerList nie usuwa zapytania ktore jest jako 1

        return $this->redirectToRoute('answerList', [
            'examId' => $examId,
            'questionId' => $questionId
        ]);
    }
}
