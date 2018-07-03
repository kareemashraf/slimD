<?php

namespace App\Controller;

use App\Entity\Leads;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\History;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

class EmailController extends Controller
{



    public function history($params)
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

        $this->send();
    }

    /**
     * @Route("/send")
     */
    public function send(){
        $entityManager = $this->getDoctrine()->getManager();
        $activelists = $entityManager->getRepository(History::class)->findOneByActive();

        foreach ($activelists as $key => $list){

            $listId = $list->getListId();
            $leads = $entityManager->getRepository(Leads::class)->findByListIdAll($listId);
            $sent_leads = $entityManager->getRepository(Leads::class)->findByListIdnotSent($listId);


            foreach ($sent_leads as $key2 => $lead){
                if ($lead->getSent() == false) {
                    $email = $lead->getEmail();
                    $name = $lead->getName();
                    $gender = $lead->getGender();

                    $old_message = array("{email}", "{name}", "{gender}");
                    $new_message = array($email, $name, $gender);

                    $from = $list->getFromtext();
                    $subject = $list->getSubjecttext();
                    $message_html = str_replace($old_message, $new_message, $list->getMessagehtml());
                    $message_text = strip_tags($message_html);


                    //send email here

                    $transport = (new Swift_SmtpTransport('email-smtp.eu-west-1.amazonaws.com', 25, 'tls'))
                        ->setUsername('')
                        ->setPassword('')
                    ;
                    $mailer = new Swift_Mailer($transport);

                    $message = (new Swift_Message($subject))
                        ->setFrom(array($from => 'Kareem is Testing it'))
                        ->setTo($email)
                        ->setBody($message_text)
                        ->addPart($message_html, 'text/html')

                    ;
                    $mailer->getTransport()->setSourceIp('8.8.8.8'); // dedecated IP here
                    $mailer->send($message);

                    //end send email

                    $lead->setSent('1'); // set sent true
                    $entityManager->persist($lead);
                    $entityManager->flush();

                }

                // maybe update here instead ?
            }

            if (empty($sent_leads)){
                foreach ($leads as $key => $lead){
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
