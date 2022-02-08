<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate("login");
    }

    public function authenticate(Request $request)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $csrf = $request->request->get('csrf');

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge("auth", $csrf),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User */
        $user = $token->getUser();

        if ($user->getStatus() == User::STATUS_NOT_ACTIVATED) {
            return new RedirectResponse($this->router->generate("activation"));
        }

        if ($user->getStatus() == User::STATUS_PASSWORD_RESET) {
            return new RedirectResponse($this->router->generate("resetPassword"));
        }

        return new RedirectResponse("/");
    }
}