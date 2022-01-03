<?php

namespace App\Tests\api;

use App\Entity\User;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Faker\Factory;

class TransactionHistoryCest {

    private User $authenticatedUser;

    public function _before(ApiTester $I) {

        $this->authenticatedUser = $I->grabUser(true);
    }

    public function getTransactionHistorySuccessfully(ApiTester $I) {

        $this->fakeTransfers($I);
        $I->haveHttpHeader('Authorization', $this->authenticatedUser->getApiToken());
        $I->sendGet('transactions');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType(
            [
                'credits' => 'array',
                'debits'  => 'array',
            ]
        );

        $debits = $I->grabDataFromResponseByJsonPath('debits')[0];
        $credits = $I->grabDataFromResponseByJsonPath('credits')[0];

        $I->assertEquals(1, count($debits));
        $I->assertEquals(1, count($credits));
    }

    private function fakeTransfers(ApiTester $I) {

        $transferService = $I->grabService('App\Service\TransferService');
        $faker = Factory::create();
        $randomUser = $I->grabUser();
        $transferService->transfer($randomUser, $this->authenticatedUser, $faker->numberBetween(100, 500));
        $transferService->transfer($this->authenticatedUser, $randomUser, $faker->numberBetween(200, 300));
    }

    public function getTransactionHistoryWithoutAuthorizationAndFail(ApiTester $I) {

        $I->sendGet('/transactions');
        $I->seeJSONResponseWithCodeAndContent(
            HttpCode::UNAUTHORIZED,
            '"error":"Authentication required to complete this request"'
        );
    }
}
