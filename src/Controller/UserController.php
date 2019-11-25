<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 17.11.2019
 * Time: 17:58
 */

namespace App\Controller;


use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        // creates a task object and initializes some data for this example
       /* $user = new User();
        $user->setId('');
        $user->setUsername('');
        $user->setRole('');
        $user->setPassword('');
        $user->setLastName('');
        $user->setFirstName('');
        $user->setEmail('');*/
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = new User([]);
        $user->setLastPasswordChange(new \DateTime('now'));
        $user->setLastLogin(new \DateTime('now'));
        $user->setDateRegistration(new \DateTime('now'));

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

      /*  if ($request->isMethod('POST')) {
           // $form->submit($request->request->get($form->getName()));
            $this->redirect('user/thankyou?'.http_build_query($request->getParameter('username')));
        }*/
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            //$username = $form->get('username')->getData();
            $data[0]=$request->request->get('username');
            $data[1] = $request->request->get('password');
            $data[2] = $request->request->get('first_name');
            $data[3] = $request->request->get('last_name');
            $data[4] = $request->request->get('email');
            $data[5] = $request->request->get('roles');
            $data[6] = $request->request->get('last_login');
            $data[7] = $request->request->get('last_password_change');
            $data[8] = $request->request->get('date_registration');

            $user = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $values = $user->getAllInformation();
            $repositoryUser = new UserRepository();
            $repositoryUser -> insert($values);

           // return $this->forward($this->generateUrl('user'));
           // return $this->redirectToRoute('/user');
        }

        return $this->render('userAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/userList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userListCreate() {
        $userInformation= new UserRepository();
        $id = $userInformation -> getQuantity();

        for( $i = 0; $i <$id;$i++) {
            $users = $userInformation->getUser($i);

            $tplArray[$i] = array (
                'id' => $i,
                'username' => $users['username'],
                'password' => $users['password'],
                'first_name' => $users['first_name'],
                'last_name' => $users['last_name'],
                'email' => $users['email'],
                'role' => $users['role'],
                'last_login' => $users['last_login']['date'],
                'last_password_change' => $users['last_password_change']['date'],
                'date_registration' => $users['date_registration']['date']
            );
        }
        return $this->render( 'userList.html.twig', array (
            'data' => $tplArray
        ) );
    }

    /**
     * @Route("/userDelete")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userDelete() {

    }
}