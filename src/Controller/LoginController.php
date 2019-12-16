<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 17.11.2019
 * Time: 17:55
 */

namespace App\Controller;


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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig_Environment;

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


        if ($form->isSubmitted() && $form->isValid()) {
            //$user = $this->getDoctrine()->getRepository(UserProvider::class)->loadUserByUsername($request->request->get('login'));
            $info = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $email = $user->getEmail();
            $password = $user->getPassword();
            $id = $userRepository->getUserIdFromAuthentication($email);

            $information = $userRepository->getUser($id);
            try {
                $goodLog = $userRepository->checkPassword($email, $password);
                session_destroy();
                session_start();
                $_SESSION['user_id']=$id;
                $_SESSION['role'] = $information['role'];
                $_SESSION['username']=$information['username'];
             //   $token = new UsernamePasswordToken($email, $password, 'main', $information['role']);

            //    $context = $this->get('security.context');
             //  $context->setToken($token);
                return $this->redirectToRoute('userList');

            }catch (InvalidPassword $e) {
                    $errors = $e->getMessage();
            }
            //todo; last login change in database or download from authentication
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
    }*/
    /**
     * @Route("/logout", name="logout")
     */
    public function logout() : Response {
        return $this->redirectToRoute('login');
    }

}