<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/emails")
     */
    public function emails()
    {

        $usr= $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('emails.html.twig', array(
            'user' => $usr,
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