<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Aws\Sqs\SqsClient;
use Psr\Log\LoggerInterface;
use App\Entity\Leads;
use App\Entity\Track;
use App\Entity\Emaillist;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\History;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Aws\CloudWatch\CloudWatchClient;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;

class EmailController extends Controller
{
    private $logger;

    public function __construct( LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function history($params)
    {

        $entityManager = $this->getDoctrine()->getManager();


        if ($params['user']->getIsActive() == true) {
            $list = $entityManager->getRepository(Emaillist::class)->findOneById($params['list_id']);

            $history = new History();
            $history->setUserId($params['user']->getId());
            $history->setList($list);
            $history->setFromtext($params['from']);
            $history->setSendername($params['sender']);
            $history->setSubjecttext($params['subject']);
            $history->setMessageHtml($params['html']);
            $history->setMessagePlaintext($params['text']);

            $entityManager->persist($history);
            $entityManager->flush();
        }

        $this->send();
    }

    /**
     * @Route("/send")
     */
    public function send()
    {

        $entityManager = $this->getDoctrine()->getManager();
        $activelists = $entityManager->getRepository(History::class)->findOneByActive();

        $key = getenv('AWS_KEY'); //key
        $secret = getenv('AWS_SECRET'); //secret

        $SesClient = new SesClient([
            'region' => 'eu-west-1',
            'version' => '2010-12-01', //'latest'
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);

        foreach ($activelists as $key => $list) {

            $listId = $list->getList();
            $sent_leads = $entityManager->getRepository(Leads::class)->findByListIdnotSent($listId);

            $this->logger->info('Emailing Campaign "'.$list->getId().'" has Started');

            foreach ($sent_leads as $key2 => $lead) {
                if ($lead->getSent() == false) {
                    $email = $lead->getEmail();
                    $name = $lead->getName();
                    $gender = $lead->getGender();

                    $old_message = array("{email}", "{name}", "{gender}");
                    $new_message = array($email, $name, $gender);

                    $from = $list->getFromtext();
                    $sender_name = $list->getSendername();
                    $subject = str_replace($old_message, $new_message, $list->getSubjecttext());
                    $message_html = str_replace($old_message, $new_message, $list->getMessagehtml());
                    $message_text = strip_tags($message_html);

                    $tracker = '<img src="http://mailgram.online/pixel/' . $list->getId() . '/' . $list->getUserId() . '/' . $email . '?image=tracking.gif" alt="">';

                    $sender_email = '"' . $sender_name . '" <' . $from . '>';
                    $recipient_emails = [$email];
                    $configuration_set = 'tracking';
                    $subject = $subject;
                    $plaintext_body = $message_text;
                    $html_body = $message_html;
                    $char_set = 'UTF-8';

                    //start sending email

                    try {
                        $result = $SesClient->sendEmail([
                            'Destination' => [
                                'ToAddresses' => $recipient_emails,
                            ],
                            'ReplyToAddresses' => [$sender_email],
                            'Source' => $sender_email,
                            'Message' => [
                                'Body' => [
                                    'Html' => [
                                        'Charset' => $char_set,
                                        'Data' => $html_body.$tracker,
                                    ],
                                    'Text' => [
                                        'Charset' => $char_set,
                                        'Data' => $plaintext_body,
                                    ],
                                ],
                                'Subject' => [
                                    'Charset' => $char_set,
                                    'Data' => $subject,
                                ],
                            ],

                            'ConfigurationSetName' => $configuration_set,
                            'Tags' => [
                                [
                                    'Name' => 'user', // REQUIRED
                                    'Value' => $list->getUserId(), // REQUIRED
                                ],
                            ]

                        ]);


                        $messageId = $result['MessageId'];
                        $messageStatus = $result['@metadata']['statusCode'];

                        if ($messageStatus == 200) {
                            $this->logger->info($key2.' Email sent to '.$recipient_emails[0].' list ID: '.$list->getId().' from: '.$from);
                            $tracking = new Track();
                            $tracking->setUserId($listId->getUserId());
                            $tracking->setCampaignId($list->getId());
                            $tracking->setSentTo($email);
                            $tracking->setMessageId($messageId);

                            $lead->setSent(true); // set sent true
                            $entityManager->persist($lead);
                            $entityManager->persist($tracking);
                            $entityManager->flush();

                        }else{
                            $this->logger->info($key2.' Email couldnt be sent to '.$recipient_emails.' list ID: '.$list->getId().' from: '.$sender_email.' and email is deleted from the list');
                            $lead->setIsActive(0); //deactivate the lead
                            $entityManager->persist($lead);
                            $entityManager->flush();
                        }

                        $this->Bounces_handler(); //deal with the bounces if any

                    } catch (AwsException $e) {
                        $this->logger->info($e->getMessage());
                        $this->logger->info("The email was not sent. Error message: " . $e->getAwsErrorMessage() . "\n");
                    }
                    //end sending the email

                }

                // maybe update here instead ?
            }

            $not_sent_leads = $entityManager->getRepository(Leads::class)->findByListIdnotSent($listId);
            if (empty($not_sent_leads)) {
                $this->logger->info('Emailing Campaign "'.$list->getId().'" has finished , resetting leeds to 0 ');
                $leads = $entityManager->getRepository(Leads::class)->findByListIdAll($listId);
                foreach ($leads as  $lead) {
                    $lead->setSent('0');
                    $list->setIsActive('0');
                    $entityManager->persist($lead);
                    $entityManager->persist($list);
                    $entityManager->flush();
                }
            }
        }

        $this->logger->info('Done!');
        return new Response('Done!');
    }

    /**
     * @Route("/bounce")
     */
    public function Bounces_handler(){

        $key = getenv('AWS_KEY'); //key
        $secret = getenv('AWS_SECRET'); //secret

        $client = new SqsClient([
            'region' => 'eu-west-1',
            'version' => 'latest',
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);

        try {
            $result = $client->receiveMessage([
                'QueueUrl' => 'https://sqs.eu-west-1.amazonaws.com/319238705108/Notification', // REQUIRED
            ]);

            if ($result['Messages']) {
                $ReceiptHandle = $result['Messages'][0]['ReceiptHandle'];
                $sqsResponse = json_decode($result['Messages'][0]['Body']);
                $sqsMessage = json_decode($sqsResponse->Message);
                if ($sqsMessage->mail) {
                    $bouncedEmail = $sqsMessage->mail->destination[0];

                    $entityManager = $this->getDoctrine()->getManager();
                    $leads = $entityManager->getRepository(Leads::class)->findByEmail($bouncedEmail);

                    $this->logger->info('Bounced Email Detected as ' . $bouncedEmail);

                    if ($leads) {
                        foreach ($leads as $lead) {
                            $lead->setIsActive(false); // deactivate the bounced lead
                            $entityManager->persist($lead);
                            $entityManager->flush();
                            $this->logger->info('Bounced Email ' . $bouncedEmail . ' has been removed');
                        }

                    } else {
                        $this->logger->info('No Bounces detected');
                    }

                    // delete sqs message
                    $client->deleteMessage([
                        'QueueUrl' => 'https://sqs.eu-west-1.amazonaws.com/319238705108/Notification', // REQUIRED
                        'ReceiptHandle' => $ReceiptHandle, // REQUIRED
                    ]);
                }
            }
        } catch (AwsException $e) {

            $this->logger->info($e->getMessage());
            $this->logger->info("there was an Error with the SQS message. Error message: " . $e->getAwsErrorMessage() . "\n");
        }
    }


}
