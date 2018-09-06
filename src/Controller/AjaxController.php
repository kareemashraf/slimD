<?php

namespace App\Controller;

use App\Entity\History;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends Controller
{
    //TODO move all the ajax functions to here


    /**
     * @Route("/ajax/stop")
     */
    public function stop_campaign(Request $request)
    {

        $userid = $request->request->get("userid");
        $id     = $request->request->get("id");
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        if ($usr->getId() == $userid){
            $entityManager = $this->getDoctrine()->getManager();
            $campaign = $entityManager->getRepository(History::class)->findOneById($id);


            $campaign->setIsActive('0'); // set send to False
            $entityManager->persist($campaign);
            $entityManager->flush();

            return new Response();
        }else{
            return false; //todo return false in v 2.0
        }

    }



    /**
     * @Route("/ajax/opened_email", name="ajax")
     */
    public function opens(Request $request)
    {
        $year = '2018'; //  $request->request->get("year");
        $usr= $this->get('security.token_storage')->getToken()->getUser();
        $entityManager = $this->getDoctrine()->getManager();
//        $opens   = $entityManager->getRepository(Track::class)->findByUserId($usr->getId());
//        $opens_perYear   = $entityManager->getRepository(Track::class)->findByUserIdYearly($usr->getId(),$year);

        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = "select   count(*) as total, DATE_FORMAT(`tracking_date`, '%b') as month from `track`  WHERE DATE_FORMAT(`tracking_date`, '%Y') = '2018' AND user_id = ".$usr->getId()." GROUP BY DATE_FORMAT(`tracking_date`, '%Y-%m') ORDER BY tracking_date asc";

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        return $this->json($result);
    }



}
