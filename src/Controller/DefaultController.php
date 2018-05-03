<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $usr= $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('dashboard.html.twig', array(
            'user' => $usr,
        ));

    }


    /**
     * @Route("/profile")
     */
    public function profile()
    {

        $usr= $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('profile.html.twig', array(
            'user' => $usr,
        ));

    }


    /**
     * @Route("/emails")
     */
    public function emails()
    {

        $usr= $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('emails.html.twig', array(
            'user' => $usr,
        ));

    }


    /**
     * @Route("/terms-and-conditions")
     */
    public function terms_and_conditions()
    {

        $usr= $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('terms-and-conditions.html.twig', array(
            'user' => $usr,
        ));

    }



}