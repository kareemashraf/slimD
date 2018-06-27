<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\History;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;


class EmailController extends Controller
{


    public function send($params)
    {

        $entityManager = $this->getDoctrine()->getManager();

        if ($params['user']->getIsActive() == true ){
            $history  = new History();
            $history->setUserId($params['user']->getId());
            $history->setListId($params['list_id']);
            $history->setFromtext($params['from']);
            $history->setSubjecttext($params['subject']);
            $history->setMessageHtml($params['html']);
            $history->setMessagePlaintext($params['text']);

            $entityManager->persist($history);
            $entityManager->flush();
        }
        var_dump($params); die();
//        return $this->render('email/index.html.twig', [
//            'controller_name' => 'EmailController',
//        ]);
    }
}
