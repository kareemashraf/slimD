<?php

namespace App\Controller;

use App\Entity\History;
use App\Entity\Leads;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use SimpleEmailServiceMessage;
use SimpleEmailService;
use Aws\CloudWatch\CloudWatchClient;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;

class AjaxController extends Controller
{
    //TODO move all the ajax functions to here


    /**
     * @Route("/ajax/stop")
     */
    public function stop_campaign(Request $request)
    {

        $userid = $request->request->get("userid");
        $id = $request->request->get("id");
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        if ($usr->getId() == $userid) {
            $entityManager = $this->getDoctrine()->getManager();
            $campaign = $entityManager->getRepository(History::class)->findOneById($id);


            $campaign->setIsActive('0'); // set send to False
            $entityManager->persist($campaign);
            $entityManager->flush();

            return new Response();
        } else {
            return false; //todo return false in v 2.0
        }

    }


    /**
     * @Route("/ajax/unsubscribe")
     */
    public function unsubscribe(Request $request){
        $email = $request->request->get("email");
        $entityManager = $this->getDoctrine()->getManager();
        $leads = $entityManager->getRepository(Leads::class)->findByEmail($email);

        if (empty($leads)){
            return new Response('<div class="alert alert-warning"><strong>Warning!</strong> this E-mail address doesnt exist.</div>');
        }
        else{
            foreach ($leads as $lead){
                $lead->setIsActive(false); // deactivate lead
                $entityManager->persist($lead);
                $entityManager->flush();
            }
            return new Response('<div class="alert alert-success"><strong>Success!</strong> you have successfully <b>unsubscribed</b> </div>');
        }



    }


    /**
     * @Route("/ajax/opened_email", name="ajax")
     */
    public function opens(Request $request)
    {
//        $method = $request->request->get("method");
//        $year = '2018'; //  $request->request->get("year");
//        $usr= $this->get('security.token_storage')->getToken()->getUser();
//        $em = $this->getDoctrine()->getManager();
//
//        if ($method == "opened") {
//
//            $RAW_QUERY = "select   count(*) as total, DATE_FORMAT(`opened_date`, '%b') as month from `track`
//                      WHERE DATE_FORMAT(`opened_date`, '%Y') = '" . $year . "'
//                      AND user_id = '" . $usr->getId() . "' AND opened = 1
//                      GROUP BY DATE_FORMAT(`opened_date`, '%Y-%m')
//                      ORDER BY opened_date asc;";
//
//            $statement = $em->getConnection()->prepare($RAW_QUERY);
//            $statement->execute();
//
//            $result = $statement->fetchAll();
//
//            return $this->json($result);
//        }
//        elseif ($method == "sent"){
//            $RAW_QUERY = "select   count(*) as total, DATE_FORMAT(`opened_date`, '%b') as month from `track`
//                          WHERE DATE_FORMAT(`sent_date`, '%Y') = '" . $year . "'
//                          AND user_id = '". $usr->getId() ."'
//                          GROUP BY DATE_FORMAT(`sent_date`, '%Y-%m')
//                          ORDER BY sent_date asc;";
//
//            $statement = $em->getConnection()->prepare($RAW_QUERY);
//            $statement->execute();
//
//            $result = $statement->fetchAll();
//
//            return $this->json($result);
//        }
//
//        return false;
    }


    /**
     * @Route("/send_email", name="send email")
     */
    public function test()
    {


//        $trigger_error = true;
//        $region_endpoint = SimpleEmailService::AWS_EU_WEST1;
//        $ses = new SimpleEmailService('', '', $region_endpoint,$trigger_error);
//
//        $m = new SimpleEmailServiceMessage();
//        $m->setConfigurationSet('tracking');
//        $m->addTo('kareem.ashraf.91@gmail.com');
//        $m->setFrom('kareem.ashraf.91@gmail.com');
//        $m->setSubject('hi');
//        $m->setMessageFromString('This is the message body.','<h1>This is the message body. </h1><a href="http://mailgram.online" >mailgram</a> ');
//
//
//        $result = $ses->sendEmail($m, false, $trigger_error);
//
//        var_dump($result); die();


//        $transport = (new Swift_SmtpTransport('email-smtp.eu-west-1.amazonaws.com', 25, 'tls'))
//            ->setUsername('')
//            ->setPassword('');
//        $mailer = new Swift_Mailer($transport);

//                    $message = (new Swift_Message($subject))
//                        ->setFrom(array($from => $sender_name))
//                        ->setTo($email)
//                        ->setBody($message_text)
//                        ->addPart($message_html.$tracker, 'text/html')
//
//                    ;
//                    $mailer->getTransport()->setSourceIp('8.8.8.8'); // dedicated IP here
//                    $result = $mailer->send($message);


//        $key = ''; //key
//        $secret = ''; //secret
//
//        $SesClient = new SesClient([
//            'region' => 'eu-west-1',
//            'version' => '2010-12-01', //'latest'
//            'credentials' => [
//                'key' => $key,
//                'secret' => $secret,
//            ],
//        ]);
//
//
//        $sender_email = '"test name" <kareem.ashraf.91@gmail.com>';
//
//        $recipient_emails = ['kareem.ashraf.91@gmail.com'];
//
//        $configuration_set = 'tracking';
//
//        $subject = 'Amazon SES test (AWS SDK for PHP)';
//        $plaintext_body = 'This email was sent with Amazon SES using the AWS SDK for PHP.';
//        $html_body = '<h1>AWS Amazon Simple Email Service Test Email</h1>' .
//            '<p>This email was sent with <a href="https://aws.amazon.com/ses/">' .
//            'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">' .
//            'AWS SDK for PHP</a>.</p>';
//        $char_set = 'UTF-8';
//
//        try {
//            $result = $SesClient->sendEmail([
//                'Destination' => [
//                    'ToAddresses' => $recipient_emails,
//                ],
//                'ReplyToAddresses' => [$sender_email],
//                'Source' => $sender_email,
//                'Message' => [
//                    'Body' => [
//                        'Html' => [
//                            'Charset' => $char_set,
//                            'Data' => $html_body,
//                        ],
//                        'Text' => [
//                            'Charset' => $char_set,
//                            'Data' => $plaintext_body,
//                        ],
//                    ],
//                    'Subject' => [
//                        'Charset' => $char_set,
//                        'Data' => $subject,
//                    ],
//                ],
//                // If you aren't using a configuration set, comment or delete the
//                // following line
//                'ConfigurationSetName' => $configuration_set,
//            ]);
//            var_dump($result);
//            $messageId = $result['MessageId'];
//            echo("Email sent! Message ID: $messageId" . "\n");
//        } catch (AwsException $e) {
//            // output error message if fails
//            echo $e->getMessage();
//            echo("The email was not sent. Error message: " . $e->getAwsErrorMessage() . "\n");
//            echo "\n";
//        }
//
//
//        die;
    }

    /**
     * @Route("/ajax/tracking", name="tracking")
     */
    public function tracking()
    {
        $key = ''; //key
        $secret = ''; //secret

        $client = new CloudWatchClient([
            'region' => 'eu-west-1',
            'version' => 'latest', // 2010-08-01 ?
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
                        'Period' => 86400,  // 24 hours
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
                        'Period' => 86400,  // 24 hours
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
                        'Period' => 86400,  // 24 hours
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
                        'Period' => 86400,  // 24 hours
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
                        'Period' => 86400,  // 24 hours
                        'Stat' => 'Sum', // REQUIRED
                        'Unit' => 'Count',
                    ],
                ],
            ],
        ]);

        return $this->json($result['MetricDataResults']);
    }


}
