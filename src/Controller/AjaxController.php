<?php

namespace App\Controller;

use App\Entity\History;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use SimpleEmailServiceMessage;
use SimpleEmailService;

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
        $method = $request->request->get("method");
        $year = '2018'; //  $request->request->get("year");
        $usr= $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        if ($method == "opened") {

            $RAW_QUERY = "select   count(*) as total, DATE_FORMAT(`opened_date`, '%b') as month from `track` 
                      WHERE DATE_FORMAT(`opened_date`, '%Y') = '" . $year . "' 
                      AND user_id = '" . $usr->getId() . "' AND opened = 1 
                      GROUP BY DATE_FORMAT(`opened_date`, '%Y-%m') 
                      ORDER BY opened_date asc;";

            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();

            $result = $statement->fetchAll();

            return $this->json($result);
        }
        elseif ($method == "sent"){
            $RAW_QUERY = "select   count(*) as total, DATE_FORMAT(`opened_date`, '%b') as month from `track`  
                          WHERE DATE_FORMAT(`sent_date`, '%Y') = '" . $year . "' 
                          AND user_id = '". $usr->getId() ."'
                          GROUP BY DATE_FORMAT(`sent_date`, '%Y-%m') 
                          ORDER BY sent_date asc;";

            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();

            $result = $statement->fetchAll();

            return $this->json($result);
        }

        return false;
    }





    /**
     * @Route("/send_email", name="send email")
     */
    public function test()
    {
        $m = new SimpleEmailServiceMessage();
        $m->setConfigurationSet('tracking');
        $m->addTo('kareem.ashraf.91@gmail.com');
        $m->setFrom('kareem.ashraf.91@gmail.com');
        $m->setSubject('hi');
        $m->setMessageFromString('This is the message body.');

        $trigger_error = true;

        $region_endpoint = SimpleEmailService::AWS_EU_WEST1;

        $ses = new SimpleEmailService('', '', $region_endpoint,$trigger_error);

        $result = $ses->sendEmail($m, false, $trigger_error);

        var_dump($m,$result); die();
    }



}
