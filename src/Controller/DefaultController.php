<?php

namespace App\Controller;

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

        return $this->render('dashboard.html.twig', array(
            'user' => $usr,
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
            $message_subject = $data['subject'];

            $params['html'] = $message_html;
            $params['text'] = $message_text;
            $params['from'] = $message_from;
            $params['subject'] = $message_subject;
            $params['list_id']= $list_id;
            $params['user'] = $usr;

            $client = new EmailController();
            $client->setContainer($this->container);
            $client->send($params);

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



}