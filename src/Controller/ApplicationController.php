<?php

namespace App\Controller;

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
}
