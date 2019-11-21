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
            $data[5] = $request->request->get('role');
            $data[6] = $request->request->get('last_login');
            $data[7] = $request->request->get('last_password_change');
            $data[8] = $request->request->get('date_registration');





            $user = $form->getData();
            $password = $form["password"]->getData();
          /*  $products = $user->getDoctrine()
                ->getRepository(User::class)->insert[$userData];*/
// ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
             $entityManager = $this->getDoctrine()->getManager();

             /*$entityManager->persist($user);
             $entityManager->flush();*/

            //var_dump($request->request->get($form->getName()));
           /* $user->setUsername($form->get('username')->getData());
            $user->setRole($form->get('role')->getData());
            $user->setPassword($form->get('password')->getData());
            $user->setLastName($form->get('last_name')->getData());
            $user->setFirstName($form->get('first_name')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setDateRegistration(new \DateTime('now'));
            $user->setLastPasswordChange(new \DateTime('now'));
            $user->setLastLogin(new \DateTime('now'));*/

           // var_dump($user->getEmail());
        $test = $user->getAllInformation();
           $repositoryUser = new UserRepository();
           $repositoryUser -> insert($test);
            print_r($test);


      //      return $this->redirectToRoute('/insertUser');
        }

        return $this->render('userAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function viewUsers() {

    }

}