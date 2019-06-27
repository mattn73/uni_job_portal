<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Entity\User;
use App\Form\SeekerProfileType;
use App\Form\SkillType;
use http\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        return $this->render('seeker/createJob.twig', [
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
        $user = $userRepo->findOneBy(array('confirmationToken' => $token));

        if (null === $user) {
            throw $this->createNotFoundException('Invalid Token');
        }
        $user->setEnabled(true);

        $userManager->updateUser($user, true);

        return $this->render('user/confirmationSucess.html.twig', [
            'email' => $user->getEmail(),
        ]);
    }


    /**
     * @Route("/register/seeker", name="seeker_registration")
     */
    public function registrationSeeker(Request $request, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return new RedirectResponse('/');
        }

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
            $user->setRoles(array('ROLE_SEEKER'));

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken(SELF::generateToken());
            }


            try {
                $this->sendConfirmationMail($user, $seeker->getFirstname(), $mailer, $logger);
            } catch (\Exception $e) {
                $logger->error($e->getMessage());
                $this->addFlash('fail', 'saved Fail');
            }

            $userManager->updateUser($user, true);

            $seeker->setUser($user);

            $em->persist($seeker);
            $em->flush();

            $this->addFlash('success', 'Profile was saved');
        }

        return $this->render('seeker/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/seeker/profile", name="seeker_profile")
     */
    public function seekerProfile(Request $request, LoggerInterface $logger)
    {
        $seekerRepo = $this->getDoctrine()->getRepository(Seeker::class);

        $seeker = $seekerRepo->findOneBy(array('user' => $this->getUser()));

        $form = $this->createForm(SeekerProfileType::class, $seeker);
        $skillForm = $this->createForm(SkillType::class);

        $skill = $seeker->getSkill();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('cv')->getData();
            if (!is_null($file)) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $_ENV['UPLOAD_DIRECTORY'],
                        $fileName
                    );
                } catch (FileException $e) {
                    $logger->error($e->getMessage());
                    $this->addFlash('fail', 'saved Fail');

                }

                $seeker->setCv($fileName);
            }
            $em = $this->getDoctrine()->getManager();

            $em->persist($seeker);
            $em->flush();

            $this->addFlash('success', 'Profile was saved');

        }

        return $this->render('seeker/profile.html.twig', [
            'form' => $form->createView(),
            'skills' => $skill,
            'skillForm' => $skillForm->createView(),
        ]);
    }

    /**
     * @Route("/skill/add", name="add_skill")
     */
    public function AddSkill(Request $request, LoggerInterface $logger)
    {
        $seekerRepo = $this->getDoctrine()->getRepository(Seeker::class);
        $seeker = $seekerRepo->findOneBy(array('user' => $this->getUser()));

        $skillForm = $this->createForm(SkillType::class);
        $skillForm->handleRequest($request);
        if ($skillForm->isSubmitted() && $skillForm->isValid()) {

            $skillName = ucwords($skillForm->getData()->getName());
            $skill = $seekerRepo = $this->getDoctrine()->getRepository(Skill::class)->findOneBy(array('name' => $skillName));

            $em = $this->getDoctrine()->getManager();

            if (is_null($skill)) {
                $skill = new Skill();
                $skill->setName($skillName);
                $em->persist($skill);

            }

            $seeker->addSkill($skill);

            $em->persist($seeker);
            $em->flush();
        }
        $skills = $seeker->getSkill();

        return $this->render('seeker/partial/skill.html.twig', [
            'skillForm' => $skillForm->createView(),
            'skills' => $skills,
        ]);
    }

    /**
     * @Route("/skill/delete", name="delete_skill")
     */
    public function removeSkill(Request $request, LoggerInterface $logger)
    {
        $id = $request->get('id');

        if (is_null($id) || !is_numeric($id)) {
            return new Response(Response::HTTP_BAD_REQUEST);
        }

        $seekerRepo = $this->getDoctrine()->getRepository(Seeker::class);
        $seeker = $seekerRepo->findOneBy(array('user' => $this->getUser()));

        $skill = $seekerRepo = $this->getDoctrine()->getRepository(Skill::class)->find($id);

        $em = $this->getDoctrine()->getManager();

        if (is_null($skill)) {
            return new Response(Response::HTTP_NOT_FOUND);
        }

        $seeker->removeSkill($skill);

        $em->persist($seeker);
        $em->flush();

        $skills = $seeker->getSkill();

        $skillForm = $this->createForm(SkillType::class);

        return $this->render('seeker/partial/skill.html.twig', [
            'skillForm' => $skillForm->createView(),
            'skills' => $skills,
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

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}



