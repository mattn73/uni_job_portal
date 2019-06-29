<?php

namespace App\Controller;

use App\Entity\JobPosting;
use App\Entity\Application;
use App\Entity\Seeker;
use App\Form\CreateJobType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobController extends AbstractController
{
    /**
     * @Route("/company/job/create", name="job_create")
     * @param Request $request
     * @return Response
     */
    public function createJob(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $company = $user->getCompany();

        //form to create job
        $form = $this->createForm(CreateJobType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jTitle = $form->get('jTitle')->getData();
            $jReference = $form->get('jReference')->getData();
            $cDate = $form->get('cDate')->getData();
            $jDesc = $form->get('jDesc')->getData();

            //create new job
            $job = new JobPosting();
            $job->setJobTitle($jTitle);
            $job->setJobReference($jReference);
            $job->setClosingDate($cDate);
            $job->setJobDescr($jDesc);
            $job->setCompany($company);

            //save db
            $em->persist($job);
            $em->flush();

            $this->addFlash('success', 'Job is saved');
        }

        return $this->render('job/createJob.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * list all jobs created by current user
     *
     * @Route("/company/job", name="job_list")
     */
    public function listJob()
    {
        $user = $this->getUser();
        $company = $user->getCompany();
        $jobRepo = $this->getDoctrine()->getRepository(JobPosting::class);
        $jobs = $jobRepo->findAllJobPostingsByCompany($company);

        return $this->render('job/listJob.twig', [
            'jobs' => $jobs
        ]);
    }

    /**
     * list all jobs
     *
     * @Route("/job", name="job_list_general")
     */
    public function jobList()
    {
        $jobRepo = $this->getDoctrine()->getRepository(JobPosting::class);
        $jobs = $jobRepo->findAllValidatedJobs();

        return $this->render('job/listJob.html.twig', [
            'jobs' => $jobs
        ]);
    }

    /**
     * @param $id
     * @return Response
     * list all jobs
     *
     * @Route("/job/{id}", name="seeker_view_application")
     */
    public function viewJob($id)
    {
        $jobRepo = $this->getDoctrine()->getRepository(JobPosting::class);
        $jobs = $jobRepo->find($id);

        return $this->render('job/viewJob.html.twig', [
            'job' => $jobs
        ]);
    }

    /**
     * Apply Job
     *
     * @Route("seeker/apply-job/{id}", name="apply_job")
     */
    public function ApplyJob($id)
    {
        $jobRepo = $this->getDoctrine()->getRepository(JobPosting::class);
        $job = $jobRepo->find($id);

        if(is_null($job)){
            return $this->render('job/apply_job.html.twig', [
                'found'=> false,
                'reason'=> false,

            ]);
        }

        $seekerRepo = $this->getDoctrine()->getRepository(Seeker::class);
        $seeker = $seekerRepo->findOneBy(array('user' => $this->getUser()));

        $application = $applicationRepo = $this->getDoctrine()->getRepository(Application::class)->exist($job,$seeker);

        if(!is_null($application)){
            return $this->render('job/apply_job.html.twig', [
                'found'=> false,
                'reason'=> true,
            ]);
        }

        $application = new Application();
        $application->setSeeker($seeker);
        $application->setJob($job);
        $application->setStatus(Application::NEW);
        $application->setNotification(true);

        $em = $this->getDoctrine()->getManager();

        $em->persist($application);
        $em->flush();

        return $this->render('job/apply_job.html.twig', [
            'found' => true,
            'job'   => $job
        ]);
    }
}
