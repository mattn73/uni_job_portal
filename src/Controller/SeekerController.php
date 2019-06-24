<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Seeker;
use App\Form\RegistrationSeekerType;
use FOS\UserBundle\Model\UserManagerInterface;

class SeekerController extends AbstractController
{
    private $userManager;

    public function __construct( UserManagerInterface $userManager)
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


            $em =  $this->getDoctrine()->getManager();

            $userManager = $this->userManager;

            $user = $userManager->createUser();

            $user->setUsername(hash('ripemd160', $seeker->getFirstname() . $seeker->getLastName()));
            $user->setEmail($seeker->getEmail());
            $user->setPlainPassword($password);
            $user->setEnabled(true);

            $userManager->updateUser($user, true);

            $seeker->setUser($user);

            $em->persist($seeker);
            $em->flush();

        }

        return $this->render('seeker/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
