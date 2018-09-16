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
use Aws\CloudWatch\CloudWatchClient;
use Aws\Exception\AwsException;

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


        $trigger_error = true;
        $region_endpoint = SimpleEmailService::AWS_EU_WEST1;
        $ses = new SimpleEmailService('', '', $region_endpoint,$trigger_error);

        $m = new SimpleEmailServiceMessage();
        $m->setConfigurationSet('tracking');
        $m->addTo('kareem.ashraf.91@gmail.com');
        $m->setFrom('kareem.ashraf.91@gmail.com');
        $m->setSubject('hi');
        $m->setMessageFromString('This is the message body.','<h1>This is the message body. </h1><a href="http://mailgram.online" >mailgram</a> ');


        $result = $ses->sendEmail($m, false, $trigger_error);

        var_dump($result); die();


    }

    /**
     * @Route("/ajax/tracking", name="tracking")
     */
    public function tracking(){
        $key = ''; //key
        $secret = ''; //secret

        $client = new CloudWatchClient([
            'region' => 'eu-west-1',
            'version' => '2010-08-01',
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);

        $result = $client->getMetricData([
            'EndTime' => strtotime(date("Y-m-d H:i:s")), // REQUIRED
            'StartTime' => strtotime(date("Y-m-d H:i:s", strtotime('-30 days'))), // REQUIRED
            'MetricDataQueries' => [ // REQUIRED
                [
                    'Id' => 's1', // REQUIRED
                    'MetricStat' => [
                        'Metric' => [ // REQUIRED
                            'Dimensions' => [
                                [
                                    'Name' => 'signed-by', // REQUIRED
                                    'Value' => 'amazonses.com', // REQUIRED
                                ],
                                // ...
                            ],
                            'MetricName' => 'Send',
                            'Namespace' => 'AWS/SES',
                        ],
                        'Period' => 86400, // 24 hours
                        'Stat' => 'Sum', // REQUIRED
                        'Unit' => 'Count',
                    ],
                ],
                [
                    'Id' => 's2', // REQUIRED
                    'MetricStat' => [
                        'Metric' => [ // REQUIRED
                            'Dimensions' => [
                                [
                                    'Name' => 'signed-by', // REQUIRED
                                    'Value' => 'amazonses.com', // REQUIRED
                                ],
                                // ...
                            ],
                            'MetricName' => 'Open',
                            'Namespace' => 'AWS/SES',
                        ],
                        'Period' => 86400, // REQUIRED
                        'Stat' => 'Sum', // REQUIRED
                        'Unit' => 'Count',
                    ],
                ],
                [
                    'Id' => 's3', // REQUIRED
                    'MetricStat' => [
                        'Metric' => [ // REQUIRED
                            'Dimensions' => [
                                [
                                    'Name' => 'signed-by', // REQUIRED
                                    'Value' => 'amazonses.com', // REQUIRED
                                ],
                                // ...
                            ],
                            'MetricName' => 'Delivery',
                            'Namespace' => 'AWS/SES',
                        ],
                        'Period' => 86400, // REQUIRED
                        'Stat' => 'Sum', // REQUIRED
                        'Unit' => 'Count',
                    ],
                ],
                [
                    'Id' => 's4', // REQUIRED
                    'MetricStat' => [
                        'Metric' => [ // REQUIRED
                            'Dimensions' => [
                                [
                                    'Name' => 'signed-by', // REQUIRED
                                    'Value' => 'amazonses.com', // REQUIRED
                                ],
                                // ...
                            ],
                            'MetricName' => 'Click',
                            'Namespace' => 'AWS/SES',
                        ],
                        'Period' => 86400, // REQUIRED
                        'Stat' => 'Sum', // REQUIRED
                        'Unit' => 'Count',
                    ],
                ],
                [
                    'Id' => 's5', // REQUIRED
                    'MetricStat' => [
                        'Metric' => [ // REQUIRED
                            'Dimensions' => [
                                [
                                    'Name' => 'signed-by', // REQUIRED
                                    'Value' => 'amazonses.com', // REQUIRED
                                ],
                                // ...
                            ],
                            'MetricName' => 'Bounce',
                            'Namespace' => 'AWS/SES',
                        ],
                        'Period' => 86400, // REQUIRED
                        'Stat' => 'Sum', // REQUIRED
                        'Unit' => 'Count',
                    ],
                ],
                [
                    'Id' => 's6', // REQUIRED
                    'MetricStat' => [
                        'Metric' => [ // REQUIRED

                            'MetricName' => 'Reputation.BounceRate',
                            'Namespace' => 'AWS/SES',
                        ],
                        'Period' => 2592000, // REQUIRED
                        'Stat' => 'Sum', // REQUIRED
                        'Unit' => 'Count',
                    ],
                ],
            ],
        ]);

        return $this->json( $result['MetricDataResults']);
    }



}
