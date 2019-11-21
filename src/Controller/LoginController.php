<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 17.11.2019
 * Time: 17:55
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginController  extends AbstractController
{
    /**
     * @Route("/article/{slug}", name="login_show", methods={"GET"})
     */
    public function show($slug)
    {
        // ...
    }
}