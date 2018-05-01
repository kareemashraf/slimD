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
        $number = mt_rand(0, 100);

        return new Response(
            '<html><body>Lucky numberr: ' . $number . '</body></html>'
        );

    }
}