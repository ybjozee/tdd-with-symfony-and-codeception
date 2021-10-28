<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class APITokenAuthenticator extends AbstractAuthenticator {

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {

        $this->userRepository = $userRepository;
    }

    public function supports(Request $request)
    : ?bool {

        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request)
    : PassportInterface {

        $apiToken = $request->headers->get('Authorization');
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException(
                'Authentication required to complete this request'
            );
        }

        return new SelfValidatingPassport(
            new UserBadge(
                $apiToken,
                fn($userIdentifier) => $this->userRepository->findOneBy(['apiToken' => $userIdentifier])
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName)
    : ?Response {

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    : ?Response {

        return new JsonResponse(
            [
                'error' => strtr($exception->getMessageKey(), $exception->getMessageData()),
            ], Response::HTTP_UNAUTHORIZED
        );
    }
}