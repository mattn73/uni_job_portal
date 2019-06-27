<?php
// src/Security/LoginFormAuthenticator.php
namespace App\Security;

use App\Entity\LoginHistory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Entity\Company;
use App\Entity\Seeker;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $entityManager;
    private $router;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $loginHistoryRepo = $this->entityManager->getRepository(LoginHistory::class);
        $userIp = $request->getClientIp();
        //check if this user is blocked based on its ip address
        $checkLogin = $this->checkIfBlock($loginHistoryRepo, $userIp);

        //if blocked
        if (!$checkLogin) {
            throw new CustomUserMessageAuthenticationException('Your account is blocked for 30 min, Please try again after');
//            return new RedirectResponse($this->router->generate('app_login'));
        }

        $userIp = $request->getClientIp();
        $lHistory = new LoginHistory();
        $lHistory->setStatus(LoginHistory::ALLOW);
        $lHistory->setUserIp($userIp);
        $this->entityManager->persist($lHistory);
        $this->entityManager->flush();

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        //get user obj
        $user = $token->getUser();

        //check user is a seeker
        $seeker = $this->entityManager->getRepository(Seeker::class)->findOneBy([
            'user' => $user
        ]);

        //check if user is a contact person
        $cPerson = $this->entityManager->getRepository(Company::class)->findOneBy([
            'user' => $user
        ]);

        if (null != $seeker) {
            return new RedirectResponse($this->router->generate('seeker'));
        } elseif (null != $cPerson) {
            return new RedirectResponse($this->router->generate('company_index'));
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $loginHistoryRepo = $this->entityManager->getRepository(LoginHistory::class);
        $userIp = $request->getClientIp();
        //check if this user is blocked based on its ip address
        $checkLogin = $this->checkIfBlock($loginHistoryRepo, $userIp);

        //if blocked
        if (!$checkLogin) {
            throw new CustomUserMessageAuthenticationException('Your account is blocked for 30 min, Please try again after');
            dd($request->getSession());
//            return new RedirectResponse($this->router->generate('app_login'));
        }

        //create a new record
        $lHistory = new LoginHistory();
        $lHistory->setStatus(LoginHistory::NOT_ALLOW);
        $lHistory->setUserIp($userIp);
        $this->entityManager->persist($lHistory);
        $this->entityManager->flush();

        //search for 3 last attempts of login
        $historyRecords = $loginHistoryRepo->findThreeLastRecordByIp($userIp);

        $count = 0;

        //loop through the attempt
        foreach ($historyRecords as $historyRecord) {
            //count fail login attempts
            if ($historyRecord->getStatus() == LoginHistory::NOT_ALLOW) {
                $count++;
            }
        }

        if ($count == LoginHistory::LOGIN_ATTEMPT_ALLOW) {
            $lHistory = new LoginHistory();
            $lHistory->setStatus(LoginHistory::BLOCK);
            $lHistory->setUserIp($userIp);
            $this->entityManager->persist($lHistory);
            $this->entityManager->flush();
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }

    private function checkIfBlock($loginHistoryRepo, $userIp)
    {
        $lastBlockRecord = $loginHistoryRepo->findLastBlockIp($userIp);

        if (null == $lastBlockRecord) {
            return true;
        }

        $blockRecordDate = $lastBlockRecord->getTimestamp();
        $dateTimeNow = new \DateTime();
        $interval = $blockRecordDate->diff($dateTimeNow);

        //if greater than 30 min or 1 hour
        if (($interval->i > 30) || ($interval->h > 0)) {
            return true;
        } else {
            return false;
        }
    }
}
