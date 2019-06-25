<?php

namespace App\Controller;

use http\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Seeker;
use App\Form\RegistrationSeekerType;
use FOS\UserBundle\Model\UserManagerInterface;

class SeekerController extends AbstractController
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/seeker", name="seeker")
     */
    public function index()
    {
        return $this->render('seeker/index.html.twig', [
            'controller_name' => 'SeekerController',
        ]);
    }


    /**
     * @Route("/seeker/register", name="seeker_registration")
     */
    public function registrationSeeker(Request $request)
    {
        $seeker = new Seeker();
        $form = $this->createForm(RegistrationSeekerType::class, $seeker);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();


            $em = $this->getDoctrine()->getManager();

            $userManager = $this->userManager;

            $user = $userManager->createUser();

            $user->setUsername(uniqid('php_'));
            $user->setEmail($seeker->getEmail());
            $user->setPlainPassword($password);
            $user->setEnabled(false);

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken(SELF::generateToken());
            }

            $userManager->updateUser($user, true);
            
            try {
                SELF::sendConfirmationMail($user, $seeker->getFirstname());
            } catch (\Exception $e) {


            }
            $seeker->setUser($user);

            $em->persist($seeker);
            $em->flush();

        }

        return $this->render('seeker/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return string
     */
    public static function generateToken()
    {
        $token = openssl_random_pseudo_bytes(16);
        return bin2hex($token);
    }

    /**
     * @param $user
     * @param $name
     * @param \Swift_Mailer $mailer
     * @param \Twig\Environment $templating
     * @throws \Exception
     */
    public static function sendConfirmationMail($user, $name)
    {
        if (null !== $user && null === $user->getConfirmationToken()) {
            $mailer = new \Swift_Mailer;
            $templating = new \Twig\Environment;

            $url = SELF::generateEmailUrl($user->getConfirmationToken());

            $message = (new \Swift_Message('Confirmation Email'))
                ->setFrom('send@jobportal.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $templating->renderView(
                    // templates/emails/registration.html.twig
                        'emails/confirmation_account.html.twig',
                        array('url' => $url,
                            'name' => $name
                        )
                    ),
                    'text/html'
                );

            try {
                $mailer->send($message);
            } catch (\Exception $e) {


            }

        } else {
            throw new \Exception("Invalid User");
        }
    }

    /**
     * @param $token
     * @return string
     */
    public static function generateEmailUrl($token)
    {
        $url = $_ENV['ROOT_URL'];
        return $url . 'confirm-account/' . $token;
    }
}



