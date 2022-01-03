<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WalletRepository::class)
 */
class Wallet {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="decimal", precision=38, scale=2)
     */
    private float $balance;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="wallet", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    public function __construct(User $user) {

        $this->balance = 1000;
        $this->user = $user;
    }

    public function getBalance()
    : float {

        return $this->balance;
    }

    public function credit(float $amount)
    : void {

        $this->balance += $amount;
    }

    public function debit(float $amount)
    : void {

        $this->balance -= $amount;
    }
}