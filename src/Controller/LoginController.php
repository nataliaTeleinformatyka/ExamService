<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 17.11.2019
 * Time: 17:55
 */

namespace App\Controller;


use App\Entity\User;
use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class LoginController  extends AbstractController
{
    /**
     * @Route("/login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function signIn(Request $request)
    {
        //$repository = $this->getDoctrine()->getRepository(User::class);
        $user = new User([]);

        /*$form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);*/

        $form = $this->createFormBuilder($user)
            ->add("login",TextType::class)
            ->add("password",PasswordType::class)
            ->add("submit",SubmitType::class, array('label' => 'Sign in'))
            ->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy($form->user);

            if ($user->getLogin() == $request->request->get('login') && $user->getPassword() == $request->request->get('password')) {
                $session = new Session();
                $session->start();
                $session->set("client", $user);
                return $this->redirectToRoute('main');
            } else return $this->redirectToRoute('login', array("message" => 'Bad login or password'));
        }

        return $this->render('login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}