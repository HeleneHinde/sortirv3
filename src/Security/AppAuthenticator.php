<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private $userRepository;
    public function __construct(UserRepository $userRepository,private UrlGeneratorInterface $urlGenerator)
    {

        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $usernameOrEmail = $request->request->get('username_or_email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $usernameOrEmail);

        $userBadge = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)
            ? new UserBadge($usernameOrEmail, function($userIdentifier) {
                return $this->userRepository->findOneByEmail($userIdentifier);
            })
            : new UserBadge($usernameOrEmail, function($userIdentifier) {
                return $this->userRepository->findOneByUsername($userIdentifier);
            });

        return new Passport(
            $userBadge,
            new PasswordCredentials($request->request->get('password', '')),
            [
                new RememberMeBadge(),
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('main_home'));

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
