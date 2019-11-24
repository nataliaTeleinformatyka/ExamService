<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 13:53
 */

namespace App\Controller;


use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/question/{id}")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $question = new Question([]);

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data[0] = $request->request->get('content');
            $data[1] = $request->request->get('max_answers');
            $data[2] = $request->request->get('is_multichoice');
            $data[3] = $request->request->get('is_file');

            $question = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $question->getAllInformation();
            $repositoryExam = new QuestionRepository();
            $repositoryExam->insert($values);

            // return $this->forward($this->generateUrl('user'));
            // return $this->redirectToRoute('/user');
        }

        return $this->render('questionAdd.html.twig', [
            'form' => $form->createView(),
            'title' => 'Questions to exam ',
        ]);
    }
    /**
     * @Route("examList/questionList/exam={id}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function questionListCreate() {
        $questionInformation= new QuestionRepository();
        $id = $questionInformation -> getQuantity();

        if($id>0) {
            for ($i = 0; $i < $id; $i++) {
                $questions = $questionInformation->getQuestion($i);
                if ($questions['is_multichoice'] == 1) {
                    $is_required = true;
                } else {
                    $is_required = false;
                }
                if ($questions['is_file'] == 1) {
                    $is_required_file = true;
                } else {
                    $is_required_file = false;
                }

                $tplArray[$i] = array(
                    'id' => $i,
                    'content' => $questions['content'],
                    'max_answers' => $questions['max_answers'],
                    'is_multichoice' => $is_required,
                    'is_file' => $is_required_file
                );
            }
        } else {
            $tplArray = array(
                'id' => 0,
                'content' => 0,
                'max_answers' => 0,
                'is_multichoice' => 0,
                'is_file' => 0
            );
        }
        return $this->render( 'questionList.html.twig', array (
            'data' => $tplArray
        ) );
    }
}
