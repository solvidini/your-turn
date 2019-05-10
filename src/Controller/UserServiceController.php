<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserServiceController extends AbstractController
{
    /**
     * @Route("/user/service", name="user_service")
     */
    public function index()
    {
        return $this->render('user_service/index.html.twig', [
            'controller_name' => 'UserServiceController',
        ]);
    }
}
