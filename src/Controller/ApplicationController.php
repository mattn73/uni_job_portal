<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Company;
use App\Entity\JobPosting;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    /**
     * @Route("/jobs", name="application")
     */
    public function allJobsAction()
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
    public function companyApplicationAction()
    {
        $applicationRepo = $this->getDoctrine()->getRepository(Application::class);
        $user = $this->getUser();

        $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy([
            'user' => $user
        ]);

        $applications = $applicationRepo->findByCompany($company);

        return $this->render('company_profile/listApplication.twig', [
            'applications' => $applications,
            'general' => true
        ]);
    }

    /**
     * @Route("/company/jobs", name="company_job")
     */
    public function companyJobAction()
    {
        $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy([
            'user' => $this->getUser()
        ]);

        $jobs = $company->getJobPostings();

        return $this->render('company_profile/listJobs.html.twig', [
            'jobs' => $jobs
        ]);
    }

    /**
     * @Route("/company/job/{id}/application", name="company_job_application")
     */
    public function companyJobApplicationAction($id)
    {


    }

    /**
     * @Route("/application/status", name="application_update_status")
     */
    public function updateStatusApplicationAction(Request $request, LoggerInterface $logger)
    {
        $id = $request->get('id');
        $status = $request->get('status');
        $statusArray = array('accept', 'reject');

        if (is_null($id) || !is_numeric($id) || is_null($id) || !in_array($status , $statusArray)) {
            return new Response(Response::HTTP_BAD_REQUEST);
        }

        $applicationRepo = $this->getDoctrine()->getRepository(Application::class);

        $application = $applicationRepo->find($id);

        if (is_null($application)) {
            return new Response(Response::HTTP_NOT_FOUND);
        }

        if($status == 'accept'){
            $status = Application::ACCEPT;
        }elseif ($status == 'reject'){
            $status = Application::REJECT;
        }

        $application->setStatus($status);

        $em = $this->getDoctrine()->getManager();

        $em->persist($application);
        $em->flush();

        return $this->render('application/application.twig', [
            'application' => $application,
            'general' => true
        ]);
    }




}
