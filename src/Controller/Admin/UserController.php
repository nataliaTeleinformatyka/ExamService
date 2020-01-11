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
//todo: redirect, potwierdz haslo
       // $repository = $this->getDoctrine()->getRepository(User::class);
        $user = new User([]);
        $user->setLastPasswordChange(new \DateTime('now'));
        $user->setLastLogin(new \DateTime('now'));
        $user->setDateRegistration(new \DateTime('now'));

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            //$entityManager = $this->getDoctrine()->getManager();

            $values = $user->getAllInformation();
            $repositoryUser = new UserRepository();
            $uid = $repositoryUser->getQuantity();

            $email=$values[3];
            $password=$values[0];
            $repositoryUser->registerUser($uid,$email,$password);
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
        $userInfo = $repo->getUser($id);
        $email = $userInfo['email'];
        if($repo->delete($id))
            $repo->deleteUserFromAuthenticationByEmail($email);

        //todo: nie moze usunac gdy istnieja powiazania userexam

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
       //  $repository = $this->getDoctrine()->getRepository(User::class);
        /*print_r($id);
        $examEn= new Exam([]);
        $examrepo = new ExamRepository();
        $efxam = $examrepo->getExam($id);
        print_r($efxam);*/
        //  $exam = new Exam([]);
        $userInformation = new UserRepository();
        $userId = (int)$request->attributes->get('id');
        $users = $userInformation->getUser($userId);
        //   print_r($exams);
        //print_r($_SESSION['user_id']);
        //print_r($examId);
        //  print_r($exams['exam_id']);
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


        setcookie("info","edit",time()+60*2);
            //$_COOKIE['info'] = "editStudent";
//            $_COOKIE['info'] = "edit";




        //  $exam->setId($examId);
        //$exam->setName($exams['name']);
        //print_r($exam);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        //  var_dump($form);
        //$exams = $form->getData();
        //print_r($exams);
        if ($form->isSubmitted() && $form->isValid()) {
            $userInfo = $form->getData();
       //     $entityManager = $this->getDoctrine()->getManager();
            $examValue = $request->attributes->get('id');
            print_r($examValue);

            //$values = $user->getAllInformation();
            $repositoryExam = new UserRepository();
          //  $repositoryExam->update($values,$userId);
          //  print_r($values);
            // return $this->redirectToRoute('examList');
        }
        return $this->render('userAdd.html.twig', [
            'form' => $form->createView(),
            'userInformation' =>$userInfoArray,
            'userId' => $userId
        ]);
    }
}