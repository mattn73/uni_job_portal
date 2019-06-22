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

class CompanyProfileController extends AbstractController
{
    /**
     * @Route("/company/profile", name="company_profile", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        //get all seeker
        $em = $this->getDoctrine()->getManager();
        $seekerRepo = $this->getDoctrine()->getRepository(Seeker::class);
        $allSeeker = $seekerRepo->findContactPerson();

        //form
        $form = $this->createForm(CompanyProfileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //get submitted data
            $postData = $form->getData();
            $seekerId = $request->get('stlseeker');
            $cEmail = $postData['companyEmail'];
            $cName = $postData['companyName'];
            $email = $postData['email'];
            $address = $postData['address'];
            $name = $postData['name'];

            //get user obj
            $seeker = $seekerRepo->find($seekerId);

            //create new company
            $nCompany = new Company();
            $nCompany->setCompanyEmail($cEmail);
            $nCompany->setAddress($address);
            $nCompany->setCompanyName($cName);
            $nCompany->setEmail($email);
            $nCompany->setUser($seeker->getUser());
            $nCompany->setName($name);

            //save to db
            $em->persist($nCompany);
            $em->flush();

            $this->addFlash(
                'notice',
                'Company record is saved !'
            );
        }

        return $this->render('company_profile/companyProfile.twig', [
            'form' => $form->createView(),
            'seekers' => $allSeeker,
        ]);
    }

    /**
     * check if contact person has aleady a company under his name
     * @Route("/company/check", name="company_check", options={"expose"=true})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkUserCompany(Request $request)
    {
        //get seeker obj
        $seekerRepo = $this->getDoctrine()->getRepository(Seeker::class);
        $seekerId = $request->request->get('seekerId');
        $seeker = $seekerRepo->find($seekerId);

        //check if user already register his company
        $companyRepo = $this->getDoctrine()->getRepository(Company::class);
        $checkCompany = $companyRepo->findOneBy([
            'user' => $seeker->getUser(),
        ]);

        if (null == $checkCompany) {

            return new JsonResponse([
                //202
                'statusCode' => Response::HTTP_ACCEPTED
            ]);
        } else {

            return new JsonResponse([
                //409
                'statusCode' => Response::HTTP_CONFLICT
            ]);
        }
    }
}
