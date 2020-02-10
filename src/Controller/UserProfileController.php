<?php

namespace App\Controller;

use App\Repository\Admin\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController {

    /**
     * @Route("userProfile", name="userProfile")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userInformationCreate() {
        if(!isset($_SESSION['role']))
            return $this->redirectToRoute("login");

        $userId = $_SESSION['user_id'];
        $userRepository = new UserRepository();
        $userInformation = $userRepository->getUser($userId);
        $tplArray[] = array(
            'first_name' => $userInformation['first_name'],
            'last_name' => $userInformation['last_name'],
            'email' => $userInformation['email'],
            'last_login' => $userInformation['last_login']['date'],
            'date_registration' => $userInformation['date_registration']['date']
        );
        return $this->render('userProfile.html.twig',[
            'user_information' => $tplArray,
            'user_id' => $userId,
        ]);
    }
}
