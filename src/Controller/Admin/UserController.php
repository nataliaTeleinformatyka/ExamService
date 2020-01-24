<?php

namespace App\Controller\Admin;

use App\Form\Admin\UserEditType;
use App\Repository\Admin\UserExamRepository;
use App\Repository\Admin\UserRepository;
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
        $user = new User([]);
        $user->setLastPasswordChange(new \DateTime('now'));
        $user->setLastLogin(new \DateTime('now'));
        $user->setDateRegistration(new \DateTime('now'));

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $values = $user->getAllInformation();
            $repositoryUser = new UserRepository();
            $uid = $repositoryUser->getIdNextUser();

            $email=$values[3];
            $password=$values[0];
            $repositoryUser->registerUser($uid,$email,$password);
            $repositoryUser->insert($uid, $values);



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

        $userRepository= new UserRepository();
        $usersId = $userRepository->getIdUsers();
        if($usersId!=0){
            $usersAmount = count($usersId);
        } else {
            $usersAmount=0;
        }

        if ($usersAmount > 0) {
            $info=true;
            for ($i = 0; $i < $usersAmount; $i++) {
                $users = $userRepository->getUser($usersId[$i]);

                //$password[$i] = $userInformation->getUserPasswordFromAuthentication($users['email']);
               // $lastLogin[$i] = $userInformation->getUserLastLoginFromAuthentication($users['email']);
              //  print_r($lastLogin[$i]);
                $tplArray[$i] = array(
                    'id' => $users['id'],
                    'first_name' => $users['first_name'],
                    'last_name' => $users['last_name'],
                    'email' => $users['email'],
                    'role' => $users['role'],
                    'group_of_students' => $users['group_of_students'],
                    'last_login' => $users['last_login']['date'],
                    'last_password_change' => $users['last_password_change']['date'],
                    'date_registration' => $users['date_registration']['date']
                );

            }
        }else {
            $info = false;
            $tplArray[] = array(
                'id' => '',
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'role' => '',
                'group_of_students' => '',
                'last_login' => '',
               // 'last_password_change' => '',
                'date_registration' => ''
            );
        }
        if( isset( $_SESSION['information'] ) && count( $_SESSION['information'] ) > 0  ) {
            $infoDelete = $_SESSION['information'];
        } else {
            $infoDelete = "";
        }
        return $this->render( 'userList.html.twig', array (
            'data' => $tplArray,
            'information' => $info,
            'infoDelete' => $infoDelete,

        ) );

    }

    /**
     * @Route("/userDelete/{userId}", name="userDelete")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userDelete(Request $request) {
        $id = $request->attributes->get('userId');
        $userRepository = new UserRepository();
        $userExamRepository = new UserExamRepository();

        $isUserExam = $userExamRepository->isUserExamForUserId($id);

        $userInfo = $userRepository->getUser($id);
        $email = $userInfo['email'];
        if($isUserExam==true){
            $_SESSION['information'][] = array( 'type' => 'error', 'message' => 'The record cannot be deleted, there are links in the database');
        } else {
            if($userRepository->delete($id))
                $userRepository->deleteUserFromAuthenticationByEmail($email);
            $_SESSION['information'][] = array( 'type' => 'ok', 'message' => 'Successfully deleted');
        }

        return $this->redirectToRoute('userList');
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("editUser/{id}", name="editUser")
     */
    public function editUser(Request $request, User $user)
    {
        $userInformation = new UserRepository();
        $userId = (int)$request->attributes->get('id');
        $users = $userInformation->getUser($userId);

        $userInfoArray = array(
            'id' => $users['id'],
            'first_name' => $users['first_name'],
            'last_name' => $users['last_name'],
            'email' => $users['email'],
            'role' => $users['role'],
            'group_of_students' => $users['group_of_students'],
            'last_login' => $users['last_login']['date'],
            'last_password_change' => $users['last_password_change']['date'],
            'date_registration' => $users['date_registration']['date']
        );


        $_SESSION['info'] = "student";

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userInfo = $form->getData();
            $user->setLastPasswordChange(new \DateTime());
            $examValue = $request->attributes->get('id');
            $values = $user->getAllInformation();

            $repositoryExam = new UserRepository();
            $repositoryExam->update($values,$userId);

            switch ($_SESSION['role']) {
                case "ROLE_ADMIN":
                    {
                        return $this->redirectToRoute('userList');
                        break;
                    }
                case "ROLE_PROFESSOR":
                    {
                        return $this->redirectToRoute('userProfile');
                        break;
                    }
                case "ROLE_STUDENT":
                    {
                        return $this->redirectToRoute('userProfile');
                        break;
                    }
            }
        }
        return $this->render('userAdd.html.twig', [
            'form' => $form->createView(),
            'userInformation' =>$userInfoArray,
            'userId' => $userId
        ]);
    }
}