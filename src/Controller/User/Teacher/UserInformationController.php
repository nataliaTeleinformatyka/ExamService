<?php

namespace App\Controller\User\Teacher;


use App\Repository\Admin\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserInformationController extends AbstractController
{
    /**
     * @Route("teacherUserList", name="teacherUserList")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userListCreate()
    {
        $info = false;
        $userInformation = new UserRepository();
        $usersId = $userInformation->getIdUsers();
        if($usersId!=0){
            $usersCount = count($usersId);
        } else {
            $usersCount=0;
        }
        if ($usersCount > 0) {
            $amount = 0;
            for ($i = 0; $i < $usersCount; $i++) {
                $user = $userInformation->getUser($usersId[$i]);
                if ($user['role'] == "ROLE_STUDENT") {
                    $info = true;

                    $tplArray[$amount] = array(
                        'id' => $i,
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'email' => $user['email'],
                        'group_of_students' => $user['group_of_students'],
                    );
                    $amount++;
                } else {
                    if($i==$usersCount-1 and $info==false) {
                        $tplArray[] = array(
                            'id' => '',
                            'first_name' => '',
                            'last_name' => '',
                            'email' => '',
                            'group_of_students' => '',
                        );
                    }
                }
            }
        } else {
            $info = false;
            $tplArray[] = array(
                'id' => '',
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'group_of_students' => '',
            );
        }
        return $this->render('teacherUserList.html.twig', array(
            'data' => $tplArray,
            'information' => $info
        ));
    }
}
