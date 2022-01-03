<?php

namespace App\Repository;

use App\Entity\TransactionRecord;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TransactionRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionRecord[]    findAll()
 * @method TransactionRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRecordRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, TransactionRecord::class);
    }

    public function getCreditTransactions(User $user)
    : array {

        return $this->findBy(
            [
                'recipient' => $user,
                'isCredit'  => true,
            ]
        );
    }

    public function getDebitTransactions(User $user)
    : array {

        return $this->findBy(
            [
                'sender' => $user,
                'isCredit' => false,
            ]
        );
    }
}
