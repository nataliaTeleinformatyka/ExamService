<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 23.11.2019
 * Time: 15:51
 */

namespace App\Controller;


use App\Entity\Answer;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController
{
    /**
     * @Route("/answerList/{id}/answer")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Answer::class);
        $question = new Answer([]);

        $form = $this->createForm(AnswerType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data[0] = $request->request->get('content');
            $data[1] = $request->request->get('is_true');
            $data[2] = $request->request->get('is_active');

            $answer = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $answer->getAllInformation();
            $repositoryAnswer = new AnswerRepository();
            $repositoryAnswer->insert($values);

            // return $this->forward($this->generateUrl('user'));
            // return $this->redirectToRoute('/user');
        }

        return $this->render('answerAdd.html.twig', [
            'form' => $form->createView(),
            'title' => 'Answers to question ',
        ]);
    }
    /**
     * @Route("/answerList/{id}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function answerListCreate() {
        $answerInformation= new AnswerRepository();
        $id = $answerInformation -> getQuantity();
        if($id>0) {
            for ($i = 0; $i < $id; $i++) {
                $answers = $answerInformation->getAnswer($i);
                if ($answers['is_true'] == 1) {
                    $is_required = true;
                } else {
                    $is_required = false;
                }
                if ($answers['is_active'] == 1) {
                    $is_required_active = true;
                } else {
                    $is_required_active = false;
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
            'data' => $tplArray
        ) );
    }
}
