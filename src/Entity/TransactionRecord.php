<?php

namespace App\Entity;

use App\Repository\TransactionRecordRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRecordRepository::class)
 */
class TransactionRecord {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactionRecords")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactionRecords")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $recipient;

    /**
     * @ORM\Column(type="decimal", precision=38, scale=2)
     */
    private float $amount;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCredit;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $happenedAt;

    public function __construct(
        User              $sender,
        User              $recipient,
        float             $amount,
        bool              $isCredit,
        DateTimeImmutable $happenedAt
    ) {

        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->amount = $amount;
        $this->isCredit = $isCredit;
        $this->happenedAt = $happenedAt;
    }

//    public function getId()
//    : ?int {
//
//        return $this->id;
//    }
}
