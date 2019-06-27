<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Company;
use App\Entity\JobPosting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    /**
     * @Route("/jobs", name="application")
     */
    public function allJobs()
    {
        $jobRepo = $this->getDoctrine()->getRepository(JobPosting::class);
        $jobs = $jobRepo->findAllValidatedJobs();

        return $this->render('application/allJobs.twig', [
            'jobs' => $jobs
        ]);
    }

    /**
     * @Route("/company/applications", name="application_company")
     */
    public function companyApplication()
    {
        $applicationRepo = $this->getDoctrine()->getRepository(Application::class);
        $user = $this->getUser();

        $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy([
            'user' => $user
        ]);

        $applications = $applicationRepo->findByCompany($company);

        return $this->render('application/allJobs.twig', [
            'applications' => $applications
        ]);
    }
}
