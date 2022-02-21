<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\InsufficientFundsException;
use App\Exception\InvalidParameterException;
use App\Exception\ParameterNotFoundException;
use App\Repository\UserRepository;
use App\Service\TransferService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends BaseController {

    /**
     * @Route("/transfer", name="transfer", methods={"POST"})
     * @throws ParameterNotFoundException|InvalidParameterException|InsufficientFundsException
     */
    public function transfer(
        Request         $request,
        UserRepository  $userRepository,
        TransferService $transferService
    )
    : JsonResponse {
        /** @var $sender User */
        $sender = $this->getUser();
        $requestBody = $request->request->all();

        $recipientEmailAddress = $this->getRequiredParameter(
            'recipient',
            $requestBody,
            'Recipient is required'
        );

        $transferAmount = $this->getRequiredNonNegativeNumber(
            'amount',
            $requestBody,
        );

        $recipient = $userRepository->findOneBy(
            [
                'email' => $recipientEmailAddress,
            ]
        );

        if (is_null($recipient)) {
            return new JsonResponse(
                [
                    'error' => 'Could not find a user with the specified email address',
                ], Response::HTTP_BAD_REQUEST
            );
        }

        $transferService->transfer($sender, $recipient, $transferAmount);

        return $this->json(
            [
                'message' => 'Transfer completed successfully',
            ]
        );
    }
}