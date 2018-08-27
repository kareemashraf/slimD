<?php

namespace App\Controller;

use App\Entity\History;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use App\Entity\Emaillist;
use App\Entity\Leads;
use App\Form\ProductType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;


//ini_set('max_execution_time', 300); //300 seconds = 5 minutes

class DefaultController extends Controller
{

    // ...
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    /**
     * @Route("/")
     */
    public function index()
    {

        $usr= $this->get('security.token_storage')->getToken()->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $lists   = $entityManager->getRepository(Emaillist::class)->findByUserId($usr->getId());
        $history = $entityManager->getRepository(History::class)->findByUserId($usr->getId());

        return $this->render('dashboard.html.twig', array(
            'user' => $usr,
            'lists' => $lists,
            'orders' =>$history,
        ));

    }


    /**
     * @Route("/profile")
     */
    public function profile(Request $request)
    {
        self::post_profile($request);

        $usr= $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('profile.html.twig', array(
            'user' => $usr,
        ));

    }

    private function post_profile(Request $request){

        $usr= $this->get('security.token_storage')->getToken()->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($usr->getId());

        if ($request->request->get("fullname")) {
            $name = $request->request->get("fullname");
            $user->setFullname($name);
        }

        if ($request->request->get("email")) {
            $email = $request->request->get("email");
            $user->setEmail($email);
        }

        if ($request->request->get("phone")) {
            $phone = $request->request->get("phone");
            $user->setPhone($phone);
        }

        if ($request->request->get("about")) {
            $about = $request->request->get("about");
            $user->setAbout($about);
        }

        if ($request->request->get("password") && $request->request->get("password2") ) {
            $password = $request->request->get("password");
            $password2 = $request->request->get("password2");

            if ($password == $password2){
                $pass = $this->encoder->encodePassword($user, $password);
                $user->setPassword($pass);
            }
        }

        $entityManager->flush(); //Do the update in DB

    }

    /**
     * @Route("/lists")
     */
    public function lists(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $usr= $this->get('security.token_storage')->getToken()->getUser();

        $email_list = new Emaillist();
        $form = $this->createForm(ProductType::class, $email_list);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('list_name')->getData();

            // $file stores the uploaded CSV file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $email_list->getFile();

            $fileName = $this->generateUniqueFileName().'.csv';
//$file->guessExtension();

            // moves the file to the directory where the CSV files are stored
            $file->move(
                $this->getParameter('email_directory'),
                $fileName
            );

            // updates the 'file' property to store the csv file name
            // instead of its contents

            $email_list->setListName($name);
            $email_list->setUserId($usr->getId());
            $email_list->setFile($fileName);
            $entityManager->persist($email_list);
            $entityManager->flush();
            $lastId = $email_list->getId();

            $row = 1;
            $emails = array();
            $gender = array();
            $name = array();
            if (($handle = fopen($this->getParameter('email_directory').'/'.$fileName , "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    $row++;
                    for ($c=0; $c < $num; $c++) {
                        if ($c%3 ==0 ){
                            $emails[] = $data[$c];
                        }
                        if ($c%2 == 0 && $c%3 !=0 ){
                            $gender[] = $data[$c];
                        }
                        if ($c%2 != 0 && $c%3 !=0 ){
                            $name[] = $data[$c];
                        }
                    }
                }
                fclose($handle);
            }

//            var_dump($emails); var_dump($gender); var_dump($name); die;  //debug mode


            foreach ($emails as $key => $email){
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $leads = new Leads();
                        $leads->setEmail($email);
                        $leads->setListId($lastId);
                        $leads->setName($name[$key]);
                        $leads->setGender($gender[$key]);

                        $entityManager->persist($leads);
                        $entityManager->flush();
                }
            }


        }

        $lists = $entityManager->getRepository(Emaillist::class)->findByUserId($usr->getId());

        return $this->render('lists.html.twig', array(
            'user' => $usr,
            'form' => $form->createView(),
            'lists' => $lists,
        ));

    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * @Route("/delete-list")
     */
    public function deleteAlist(Request $request)
    {
        if ($request->request->get("id")) {
            $entityManager = $this->getDoctrine()->getManager();
            $usr = $this->get('security.token_storage')->getToken()->getUser();
            $id = $request->request->get("id");
            $list = $entityManager->getRepository(Emaillist::class)->findOneById($id);

            if ($list->getUserid() == $usr->getId()) {

                $list->setIsActive(0);
                $entityManager->persist($list);
                $entityManager->flush();

                return new Response(); //return true;

            } else {
                return false; //TODO: how to return false in Symfony 4.0
            }

        }
        return new Response();
    }

    /**
     * @Route("/emails/{id}")
     */
    public function emails(Request $request, $id = NULL)
    {

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $lists = $entityManager->getRepository(Emaillist::class)->findByUserId($usr->getId());


        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('from', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'width: 70%;', 'placeholder' => 'name@example.com'], // for input
                'label_attr' => ['class' => 'col-sm-2 col-form-label col-form-label-sm'], // for label
            ])
            ->add('sender_name', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'width: 70%;', 'placeholder' => 'Sender Name'], // for input
                'label_attr' => ['class' => 'col-sm-2 col-form-label col-form-label-sm'], // for label
            ])
            ->add('subject', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'width: 70%;'], // for input
                'label_attr' => ['class' => 'col-sm-2 col-form-label col-form-label-sm'], // for label
            ])
            ->add('message_html', CKEditorType::class, [
                'config' => array('toolbar' => 'full'),
                'constraints' => array(
                    new NotBlank(),
                ),
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $params = array();
            $data = $form->getData();

            if ($request->request->get("email_list") && !empty($request->request->get("email_list"))) {
                $list_id = $request->request->get("email_list");
            }

            $message_html = $data['message_html'];
            $message_text = strip_tags($message_html);
            $message_from = $data['from'];
            $message_sender = $data['sender_name'];
            $message_subject = $data['subject'];

            $params['html'] = $message_html;
            $params['text'] = $message_text;
            $params['from'] = $message_from;
            $params['sender'] = $message_sender;
            $params['subject'] = $message_subject;
            $params['list_id']= $list_id;
            $params['user'] = $usr;

            $client = new EmailController();
            $client->setContainer($this->container);
            $client->history($params);

            return $this->redirectToRoute('app_default_index'); // return to homepage
        }


        if ($id) {
            $list = $entityManager->getRepository(Emaillist::class)->findOneById($id);
            if ($list && $list->getUserid() == $usr->getId()) {
                return $this->render('emails.html.twig', array(
                    'user' => $usr,
                    'list' => $list,
                    'lists' => $lists,
                    'form'=> $form->createView(),
                ));
            }
        }
        return $this->render('emails.html.twig', array(
            'user' => $usr,
            'lists' => $lists,
            'form'=> $form->createView(),
        ));

    }




    /**
     * @Route("/terms-and-conditions")
     */
    public function terms_and_conditions()
    {

        $usr= $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('terms-and-conditions.html.twig', array(
            'user' => $usr,
        ));

    }

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
     * @Route("/pixel/{id}/{userid}")
     */
    public function pixel($id= NULL, $userid=NULL){

        header('Content-Type: image/gif');
        readfile('assets/images/tracking.gif');

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        $ip = $_SERVER['REMOTE_ADDR'];

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$userAgent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($userAgent,0,4)))
        {
            $device = 'Mobile';
        }
        else
        {
            $device = 'Desktop';
        }

        $txt = $date.",". $ip." , ".$userAgent." , ".$device;


        //to be continued :D
        

        die($id);
    }



}
