<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SeekerProfileType;
use http\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Seeker;
use App\Form\RegistrationSeekerType;
use FOS\UserBundle\Model\UserManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        return $this->render('seeker/index.twig', [
            'controller_name' => 'SeekerController',
        ]);
    }

    /**
     * @Route("/confirm-account/{token}", name="confirm_password")
     */
    public function confirmUser($token)
    {
        $userManager = $this->userManager;

        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->findOneBy(array('confirmationToken'=> $token));

        if(null  === $user){
            throw $this->createNotFoundException('Invalid Token');
        }
        $user->setEnabled(true);

        $userManager->updateUser($user, true);

        return $this->render('user/confirmationSucess.html.twig', [
            'email' => $user->getEmail(),
        ]);
    }


    /**
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @Route("/register/seeker", name="seeker_registration")
     */
    public function registrationSeeker(Request $request,\Swift_Mailer $mailer, LoggerInterface $logger)
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
            $user->setRoles('ROLE_SEEKER');

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken(SELF::generateToken());
            }


            try {
                $this->sendConfirmationMail($user, $seeker->getFirstname(),$mailer,  $logger);
            } catch (\Exception $e) {
                $logger->error($e->getMessage());
            }

            $userManager->updateUser($user, true);

            $seeker->setUser($user);

            $em->persist($seeker);
            $em->flush();

        }

        return $this->render('seeker/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/seeker/profile", name="seeker_profile")
     */
    public function seekerProfile()
    {
        $seekerRepo = $this->getDoctrine()->getRepository(Seeker::class);

        $seeker = $seekerRepo->findOneBy(array('user' => $this->getUser()));

        $form = $this->createForm(SeekerProfileType::class, $seeker);

        return $this->render('seeker/profile.html.twig', [
            'controller_name' => 'SeekerController',
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
     * @throws \Exception
     */
    public function sendConfirmationMail($user, $name, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
        if (null !== $user && null !== $user->getConfirmationToken()) {
            $url = SELF::generateEmailUrl($user->getConfirmationToken());

            $message = (new \Swift_Message('Confirmation Email'))
                ->setFrom('send@jobportal.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
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
                $logger->error($e->getMessage());
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
        return $url . '/confirm-account/' . $token;
    }
}



