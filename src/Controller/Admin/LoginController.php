<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 17.11.2019
 * Time: 17:55
 */

namespace App\Controller\Admin;


use App\Entity\Admin\User;
use App\Form\LoginType;
use App\Repository\Admin\UserRepository;
use App\Security\UserProvider;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController  extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {

        $user = new User([]);
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);
        $userRepository = new UserRepository();
        $errors = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        // $lastUsername = $authenticationUtils->getLastUsername();
        /*$form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);*/

        /*$form = $this->createFormBuilder($user)
            ->add("username",TextType::class)
            ->add("password",PasswordType::class)
            ->add("submit",SubmitType::class, array('label' => 'Sign in'))
            ->getForm();
        $form->handleRequest($request);*/
        if ($form->isSubmitted() && $form->isValid()) {
            //$user = $this->getDoctrine()->getRepository(UserProvider::class)->loadUserByUsername($request->request->get('login'));
            $info = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $email = $user->getEmail();
            $password = $user->getPassword();
            try {
                $goodLog = $userRepository->checkPassword($email, $password);
            }catch (InvalidPassword $e) {
                    $errors = $e->getMessage();
            }
            //todo; last login change in database or download from authentication
            //return $this->redirectToRoute('userList');
        }
        return $this->render('login.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

          /*  if ($user[0]['username'] == $request->request->get('username') && $user[0]['password'] == $request->request->get('password')) {
                $session = new Session();
                $session->start();
                $session->set("client", $user);
                $_SESSION['username'] = $request->request->get('username');
                return $this->redirectToRoute('user');
            }//todo: else return $this->redirectToRoute('login', array("message" => 'Bad login or password'));
        }

        return $this->render('login.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout() : Response {}

}