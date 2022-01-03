<?php

namespace App\Tests\unit;

use App\Entity\User;
use App\Exception\InsufficientFundsException;
use App\Service\TransferService;
use App\Tests\UnitTester;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;

class TransferServiceCest {

    private Generator $faker;

    public function _before(UnitTester $I) {

        $this->faker = Factory::create();
    }

    public function handleTransferSuccessfully(UnitTester $I) {

        $sender = new User($this->faker->firstName(), $this->faker->lastName(), $this->faker->email());
        $recipient = new User($this->faker->firstName(), $this->faker->lastName(), $this->faker->email());
        $amount = $this->faker->numberBetween(100, 1000);

        $entityManager = Stub::makeEmpty(
            EntityManagerInterface::class,
            [],
            [
                'persist' => Expected::exactly(4),
                'flush'   => Expected::once(),
            ]
        );

        $transferService = new TransferService($entityManager);
        $transferService->transfer($sender, $recipient, $amount);
        $I->assertEquals(1000 - $amount, $sender->getWallet()->getBalance());
        $I->assertEquals(1000 + $amount, $recipient->getWallet()->getBalance());
    }

    public function makeTransferOfAmountExceedingWalletBalanceAndFail(UnitTester $I) {

        $I->expectThrowable(InsufficientFundsException::class, function () {

            $sender = new User($this->faker->firstName(), $this->faker->lastName(), $this->faker->email());
            $recipient = new User($this->faker->firstName(), $this->faker->lastName(), $this->faker->email());
            $amount = $this->faker->numberBetween(10000, 100000);

            $entityManager = Stub::makeEmpty(EntityManagerInterface::class);

            $transferService = new TransferService($entityManager);
            $transferService->transfer($sender, $recipient, $amount);
        });

    }
}
