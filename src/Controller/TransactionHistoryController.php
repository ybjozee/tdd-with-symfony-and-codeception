<?php

namespace App\Controller;

use App\Repository\TransactionRecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionHistoryController extends AbstractController {

    /**
     * @Route("/transactions", name="get_transaction_history", methods={"GET"})
     */
    public function getTransactionHistory(TransactionRecordRepository $transactionRecordRepository)
    : Response {

        $user = $this->getUser();

        return $this->json(
            [
                'debits'  => $transactionRecordRepository->getDebitTransactions($user),
                'credits' => $transactionRecordRepository->getCreditTransactions($user),
            ]
        );
    }
}
