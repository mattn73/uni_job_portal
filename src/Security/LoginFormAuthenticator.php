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
        $userIp = $request->getClientIp();
        $lHistory = new LoginHistory();
        $lHistory->setStatus(false);
        $lHistory->setUserIp($userIp);
        $this->entityManager->persist($lHistory);
        $this->entityManager->flush();

        $checkAttempt = $this->entityManager->getRepository(LoginHistory::class)->findBy([
            'UserIp' => $userIp
        ]);

        if (count($checkAttempt) > 3) {

        }

        return new RedirectResponse($this->router->generate('app_login'));
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }
}
