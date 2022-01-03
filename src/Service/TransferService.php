<?php

namespace App\Service;

use App\Entity\TransactionRecord;
use App\Entity\User;
use App\Exception\InsufficientFundsException;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class TransferService {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {

        $this->em = $em;
    }

    public function transfer(User $sender, User $recipient, float $amount)
    : void {

        $senderWallet = $sender->getWallet();

        if ($senderWallet->getBalance() < $amount) {
            throw new InsufficientFundsException;
        }

        $recipientWallet = $recipient->getWallet();

        $transactionDatetime = new DateTimeImmutable();

        $senderWallet->debit($amount);
        $recipientWallet->credit($amount);

        $debitRecord = new TransactionRecord($sender, $recipient, $amount, false, $transactionDatetime);
        $creditRecord = new TransactionRecord($sender, $recipient, $amount, true, $transactionDatetime);

        $this->em->persist($debitRecord);
        $this->em->persist($creditRecord);

        $this->em->persist($senderWallet);
        $this->em->persist($recipientWallet);

        $this->em->flush();
    }
}