<?php

namespace App\Controller;

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

        $key = ''; //key
        $secret = ''; //secret

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

//                    $tracker = '<img src="http://mailgram.online/pixel/' . $list->getId() . '/' . $list->getUserId() . '/' . $email . '?image=tracking.gif" alt="">';

                    $sender_email = '"' . $sender_name . '" <' . $from . '>';
                    $recipient_emails = [$email];
                    $configuration_set = 'tracking';
                    $subject = $subject;
                    $plaintext_body = $message_text;
                    $html_body = $message_html;
                    $char_set = 'UTF-8';

                    //send email here


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
                                        'Data' => $html_body,
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
                        ]);
                        var_dump($result);
                        $messageId = $result['MessageId'];
                        $messageStatus = $result['@metadata']['statusCode'];

                        if ($messageStatus == 200) {
                            $tracking = new Track();
                            $tracking->setUserId($listId->getUserId());
                            $tracking->setCampaignId($list->getId());
                            $tracking->setSentTo($email);
                            //todo: store the $messageId

                            $lead->setSent('1'); // set sent true
                            $entityManager->persist($lead);
                            $entityManager->persist($tracking);
                            $entityManager->flush();
                        }else{
                            //message is not sent
                            //TODO: Delete the lead;
                        }

                    } catch (AwsException $e) {
                        // output error message if fails
                        echo $e->getMessage();
                        echo("The email was not sent. Error message: " . $e->getAwsErrorMessage() . "\n");
                        echo "\n";
                    }
                    //end send email

                }

                // maybe update here instead ?
            }

            $not_sent_leads = $entityManager->getRepository(Leads::class)->findByListIdnotSent($listId);
            if (empty($not_sent_leads)) {
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


    }


}
