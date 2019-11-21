<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 20.11.2019
 * Time: 12:34
 */

namespace App\Controller;


use App\Entity\Exam;
use App\Form\ExamType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExamController extends AbstractController
{
    /**
     * @Route("/exam")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {

   //     $repository = $this->getDoctrine()->getRepository(Exam::class);
        $user = new Exam([]);

        $form = $this->createForm(ExamType::class, $user);
        $form->handleRequest($request);

       /* if ($request->isMethod('POST')) {
            // $form->submit($request->request->get($form->getName()));
            $this->redirect('user/thankyou?' . http_build_query($request->getParameter('username')));
        }*/
        if ($form->isSubmitted() && $form->isValid()) {

            $examData = $form->getData();
            return $this->redirectToRoute('user_added_success');
        }

        return $this->render('userAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}