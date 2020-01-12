<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 11.01.2020
 * Time: 13:46
 */

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
        $id = $userInformation->getQuantity();
        if ($id > 0) {
            $amount = 0;
            for ($i = 0; $i < $id; $i++) {
                $user = $userInformation->getUser($i);
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
                    $tplArray[] = array(
                        'id' => '',
                        'first_name' => '',
                        'last_name' => '',
                        'email' => '',
                        'group_of_students' => '',
                    );
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
