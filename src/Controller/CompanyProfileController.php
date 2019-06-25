<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Seeker;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CompanyProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;

class CompanyProfileController extends AbstractController
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/company/register", name="company_register", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //form
        $form = $this->createForm(CompanyProfileType::class);
        $form->handleRequest($request);
        //new company
        $company = new Company();

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $companyEmail = $form->get('companyEmail')->getData();
            $companyName = $form->get('companyName')->getData();
            $cpName = $form->get('cpName')->getData();
            $cpEmail = $form->get('cpEmail')->getData();
            $pAddress = $form->get('pAddress')->getData();
            //create user
            $userManager = $this->userManager;
            $user = $userManager->createUser();
            $user->setUsername(hash('ripemd160', $companyName.$companyEmail));
            $user->setEmail($cpEmail);
            $user->setPlainPassword($password);
            $user->setEnabled(true);
            $userManager->updateUser($user, true);
            //create company
            $company->setUser($user);
            $company->setCompanyEmail($companyEmail);
            $company->setCompanyName($companyName);
            $company->setName($cpName);
            $company->setEmail($cpEmail);
            $company->setAddress($pAddress);
            $em->persist($company);
            $em->flush();

            $this->addFlash('success', 'Contact person and Company was saved');
        }

        return $this->render('company_profile/companyProfile.twig', [
            'form' => $form->createView(),
        ]);
    }
}
