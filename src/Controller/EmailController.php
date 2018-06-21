<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EmailController extends Controller
{

    public function send($params)
    {
       var_dump($params); die();
//        return $this->render('email/index.html.twig', [
//            'controller_name' => 'EmailController',
//        ]);
    }
}
