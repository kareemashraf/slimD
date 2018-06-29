<?php

namespace App\Controller;

use App\Entity\Leads;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\History;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;


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
//            var_dump($list);
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

//                    echo "========= " . $key2 . " ==============</br>";
//                    echo "from: " . $from . "</br>";
//                    echo "subject: " . $subject . "</br> ";
//                    echo $email . "</br> ";
//                    echo $name . " </br>";
//                    echo $gender . "</br>";
//                    echo "message: " . $message_text . "</br>";

                    //send email here




                    //end send email

                    $lead->setSent('1'); // set sent true
                    $entityManager->persist($lead);
                    $entityManager->flush();

                }


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
//        $this->send_smtp(\Swift_Mailer );
//        die('now what ?');

    }


    public function send_smtp(\Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('kareem.ashraf.91@gmail.com')
            ->setBody('test email content')
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;

        $mailer->send($message);

    }

}
