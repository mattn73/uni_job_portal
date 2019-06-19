<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CompanyProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CompanyProfileController extends AbstractController
{
    /**
     * @Route("/company/profile", name="company_profile")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $form = $this->createForm(CompanyProfileType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            dd();
        }

        return $this->render('company_profile/companyProfile.twig', [
            'form' => $form->createView()
        ]);
    }
}
