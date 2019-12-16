<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 17.11.2019
 * Time: 17:58
 */

namespace App\Controller\Admin;


use App\Repository\Admin\UserRepository;
use App\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Admin\User;
use App\Form\Admin\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
   /* public function adminDashboard()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // or add an optional message - seen by developers
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
    }*/
    /**
     * @Route("/user", name="user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        //todo: jesli admin to moze dodac wszystkich, jesli nauczyciel to moze dodac tylko uczniow

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = new User([]);
        $user->setLastPasswordChange(new \DateTime('now'));
        $user->setLastLogin(new \DateTime('now'));
        $user->setDateRegistration(new \DateTime('now'));

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();

            $values = $user->getAllInformation();
            $repositoryUser = new UserRepository();
            print_r($values);
            $uid = $repositoryUser->getQuantity();
            $email=$values[4];
            $password=$values[1];
            $username=$values[0];

            $repositoryUser->registerUser($uid,$email,$password,$username);
            $repositoryUser->insert($values);



            // return $this->forward($this->generateUrl('user'));
           // return $this->redirectToRoute('/userList');
        }

        return $this->render('userAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/userList", name="userList")
     */
    public function userListCreate() {

        $userInformation= new UserRepository();
        $id = $userInformation -> getQuantity();
        if ($id > 0) {
            $info=true;
            for ($i = 0; $i < $id; $i++) {
                $users = $userInformation->getUser($i);

                $password[$i] = $userInformation->getUserPasswordFromAuthentication($users['email']);
                $lastLogin[$i] = $userInformation->getUserLastLoginFromAuthentication($users['email']);
                print_r($lastLogin[$i]);
                $tplArray[$i] = array(
                    'id' => $i,
                    'username' => $users['username'],
                    'password' => $password[$i],
                    'first_name' => $users['first_name'],
                    'last_name' => $users['last_name'],
                    'email' => $users['email'],
                    'role' => $users['role'],
                    'class' => $users['class'],
                    'last_login' => $users['last_login']['date'],
                    'last_password_change' => $users['last_password_change']['date'],
                    'date_registration' => $users['date_registration']['date']
                );

            }
        }else {
            $info = false;
            $tplArray[] = array(
                'id' => '',
                'username' => '',
                'password' => '',
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'role' => '',
                'class' => '',
                'last_login' => '',
                'last_password_change' => '',
                'date_registration' => ''
            );
        }
        return $this->render( 'userList.html.twig', array (
            'data' => $tplArray,
            'information' => $info,
        ) );

    }

    /**
     * @Route("/userDelete/{userId}", name="userDelete")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userDelete(Request $request) {
        $id = $request->attributes->get('userId');
        print_r($id);
        $repo = new UserRepository();
        if($repo->delete($id))
            $repo->deleteUserFromAuthentication($id);

        //todo: nie moze usunac gdy istnieja powiazania

        return $this->redirectToRoute('userList');
    }
}