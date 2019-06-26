<?php

namespace App\Controller;

use App\Entity\JobPosting;
use App\Form\CreateJobType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobController extends AbstractController
{
    /**
     * @Route("/job/create", name="job_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createJob(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

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

            //save db
            $em->persist($job);
            $em->flush();
        }

        return $this->render('job/createJob.twig', [
            'form' => $form->createView()
        ]);
    }
}
