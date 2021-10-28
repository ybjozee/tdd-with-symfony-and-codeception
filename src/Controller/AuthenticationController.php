<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\AuthenticationException;
use App\Exception\ParameterNotFoundException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends BaseController {

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @throws ParameterNotFoundException
     */
    public function register(
        Request                     $request,
        EntityManagerInterface      $em,
        UserPasswordHasherInterface $passwordHasher
    )
    : JsonResponse {

        $requestBody = $request->request->all();

        $firstName = $this->getRequiredParameter('firstName', $requestBody, 'First name is required');
        $lastName = $this->getRequiredParameter('lastName', $requestBody, 'Last name is required');
        $emailAddress = $this->getRequiredParameter('emailAddress', $requestBody, 'Email address is required');
        $password = $this->getRequiredParameter('password', $requestBody, 'Password is required');

        $user = new User($firstName, $lastName, $emailAddress);

        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        return $this->json(
            [
                'message' => 'Account created successfully',
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @throws ParameterNotFoundException|AuthenticationException
     */
    public function login(
        Request                     $request,
        UserRepository              $userRepository,
        EntityManagerInterface      $em,
        UserPasswordHasherInterface $passwordHasher
    )
    : JsonResponse {

        $requestBody = $request->request->all();

        $emailAddress = $this->getRequiredParameter('emailAddress', $requestBody, 'Email address is required');
        $password = $this->getRequiredParameter('password', $requestBody, 'Password is required');

        $user = $userRepository->findOneBy(['email' => $emailAddress]);

        if (is_null($user) || !$passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException();
        }

        $apiToken = bin2hex(random_bytes(32));
        $user->setApiToken($apiToken);
        $em->persist($user);
        $em->flush();

        return $this->json(
            [
                'token' => $apiToken,
            ]
        );
    }
}