<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Seeker;
use App\Entity\User;
use App\Form\CompanyUpdateProfileType;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CompanyProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use Psr\Log\LoggerInterface;

class CompanyProfileController extends AbstractController
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/company/index", name="company_index", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function companyIndex(Request $request)
    {
        //get current user logged in
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy([
            'user' => $user
        ]);

        //form to update
        $form = $this->createForm(CompanyUpdateProfileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $companyEmail = $form->get('cpEmail')->getData();
            $companyName = $form->get('companyName')->getData();
            $cpName = $form->get('cpName')->getData();
            $pAddress = $form->get('pAddress')->getData();

            //update company
            $company->setCompanyEmail($companyEmail);
            $company->setCompanyName($companyName);
            $company->setName($cpName);
            $company->setAddress($pAddress);
            $em->persist($company);
            $em->flush();

            $this->addFlash('update', 'Contact person and Company was updated');
        }

        return $this->render('company_profile/index.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/company", name="company_register", options={"expose"=true})
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @param LoggerInterface $logger
     * @return Response
     */
    public function companyRegister(Request $request, \Swift_Mailer $mailer, LoggerInterface $logger)
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
            $user->setUsername(hash('ripemd160', $companyName . $companyEmail));
            $user->setEmail($companyEmail);
            $user->setPlainPassword($password);
            $user->setEnabled(false);
            $user->setRoles(['ROLE_COMPANY']);

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken(SELF::generateToken());
            }

            try {
                $this->sendConfirmationMail($user, $cpName, $mailer, $logger);
            } catch (\Exception $e) {
                $logger->error($e->getMessage());
            }

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

        return $this->render('company_profile/companyRegistration.twig', [
            'form' => $form->createView(),
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
     * @param $user
     * @param $name
     * @param \Swift_Mailer $mailer
     * @param LoggerInterface $logger
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
     * @return string
     */
    public static function generateToken()
    {
        $token = openssl_random_pseudo_bytes(16);
        return bin2hex($token);
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
}
