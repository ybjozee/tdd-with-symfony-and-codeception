<?php

namespace App\Controller;

use App\Exception\InvalidParameterException;
use App\Exception\ParameterNotFoundException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends BaseController {

    /**
     * @Route("/transfer", name="transfer", methods={"POST"})
     * @throws ParameterNotFoundException|InvalidParameterException
     */
    public function transfer(
        Request                $request,
        UserRepository         $userRepository,
        EntityManagerInterface $em
    )
    : JsonResponse {

        $sender = $this->getUser();
        $senderWallet = $sender->getWallet();

        $requestBody = $request->request->all();

        $recipientEmailAddress = $this->getRequiredParameter(
            'recipient',
            $requestBody,
            'Recipient is required'
        );

        $transferAmount = $this->getRequiredParameter(
            'amount',
            $requestBody,
            'Amount is required'
        );

        $transferAmount = $this->getRequiredNonNegativeNumber(
            'amount',
            $requestBody,
        );

        if ($transferAmount > $senderWallet->getBalance()) {
            return new JsonResponse(
                [
                    'error' => 'Insufficient funds available to complete this request',
                ], Response::HTTP_BAD_REQUEST
            );
        }

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

        $recipientWallet = $recipient->getWallet();

        $senderWallet->debit($transferAmount);
        $recipientWallet->credit($transferAmount);

        $em->persist($senderWallet);
        $em->persist($recipientWallet);
        $em->flush();

        return $this->json(
            [
                'message' => 'Transfer completed successfully',
            ]
        );
    }
}