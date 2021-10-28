<?php

namespace App\Tests\api;

use App\Entity\Wallet;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Faker\Factory;
use Faker\Generator;

class TransferCest {

    private Generator $faker;

    public function _before(ApiTester $I) {

        $this->faker = Factory::create();
    }

    public function makeTransferSuccessfully(ApiTester $I) {

        $authenticatedUser = $I->grabUser(true);
        $senderWalletBalance = $authenticatedUser->getWallet()->getBalance();
        $recipient = $I->grabUser();
        $recipientWalletBalance = $recipient->getWallet()->getBalance();
        $amountToTransfer = $this->faker->numberBetween(100, 900);

        $I->haveHttpHeader('Authorization', $authenticatedUser->getApiToken());
        $I->sendPost('/transfer', [
            'recipient' => $recipient->getEmail(),
            'amount'    => $amountToTransfer,
        ]);

        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::OK,
            '"message":"Transfer completed successfully"'
        );

        $I->seeInRepository(Wallet::class, [
            'user'    => $authenticatedUser,
            'balance' => $senderWalletBalance - $amountToTransfer,
        ]);

        $I->seeInRepository(Wallet::class, [
            'user'    => $recipient,
            'balance' => $recipientWalletBalance + $amountToTransfer,
        ]);
    }

    public function makeTransferWithoutAuthorizationHeaderAndFail(ApiTester $I) {

        $I->sendPost('/transfer');
        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::UNAUTHORIZED,
            '"error":"Authentication required to complete this request"'
        );
    }

    public function makeTransferWithoutRecipientAndFail(ApiTester $I) {

        $authenticatedUser = $I->grabUser(true);
        $I->haveHttpHeader('Authorization', $authenticatedUser->getApiToken());
        $I->sendPost('/transfer', [
            'amount' => $this->faker->numberBetween(0, 900),
        ]);

        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::BAD_REQUEST,
            '"error":"Recipient is required"'
        );
    }

    public function makeTransferWithoutAmountAndFail(ApiTester $I) {

        $authenticatedUser = $I->grabUser(true);
        $I->haveHttpHeader('Authorization', $authenticatedUser->getApiToken());
        $recipient = $I->grabUser();
        $I->sendPost('/transfer', [
            'recipient' => $recipient->getEmail(),
        ]);

        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::BAD_REQUEST,
            '"error":"Amount is required"'
        );
    }

    public function makeTransferWithNonNumericAmountAndFail(ApiTester $I) {

        $authenticatedUser = $I->grabUser(true);
        $recipient = $I->grabUser();

        $I->haveHttpHeader('Authorization', $authenticatedUser->getApiToken());
        $I->sendPost('/transfer', [
            'recipient' => $recipient->getEmail(),
            'amount'    => $this->faker->randomLetter(),
        ]);

        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::BAD_REQUEST,
            '"error":"Amount must be a number"'
        );
    }

    public function makeTransferWithNegativeAmountAndFail(ApiTester $I) {

        $authenticatedUser = $I->grabUser(true);
        $recipient = $I->grabUser();

        $I->haveHttpHeader('Authorization', $authenticatedUser->getApiToken());
        $I->sendPost('/transfer', [
            'recipient' => $recipient->getEmail(),
            'amount'    => $this->faker->numberBetween() * -1,
        ]);

        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::BAD_REQUEST,
            '"error":"Amount cannot be negative"'
        );
    }

    public function makeTransferOfAmountExceedingWalletBalanceAndFail(ApiTester $I) {

        $authenticatedUser = $I->grabUser(true);
        $recipient = $I->grabUser();

        $I->haveHttpHeader('Authorization', $authenticatedUser->getApiToken());
        $I->sendPost('/transfer', [
            'recipient' => $recipient->getEmail(),
            'amount'    => $this->faker->numberBetween(2000, 100000),
        ]);

        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::BAD_REQUEST,
            '"error":"Insufficient funds available to complete this request"'
        );
    }

    public function makeTransferToNonExistentUserAndFail(ApiTester $I) {

        $authenticatedUser = $I->grabUser(true);
        $I->haveHttpHeader('Authorization', $authenticatedUser->getApiToken());
        $I->sendPost('/transfer', [
            'recipient' => $this->faker->email(),
            'amount'    => $this->faker->numberBetween(100, 900),
        ]);

        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::BAD_REQUEST,
            '"error":"Could not find a user with the specified email address"'
        );
    }
}