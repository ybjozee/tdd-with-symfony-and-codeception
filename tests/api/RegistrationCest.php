<?php

namespace App\Tests\api;

use App\Entity\User;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Faker\Factory;
use Faker\Generator;

class RegistrationCest {

    private Generator $faker;

    public function _before(ApiTester $I) {

        $this->faker = Factory::create();
    }

    public function registerSuccessfully(ApiTester $I) {

        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $emailAddress = $this->faker->email();
        $I->sendPost(
            '/register',
            [
                'firstName'    => $firstName,
                'lastName'     => $lastName,
                'emailAddress' => $emailAddress,
                'password'     => $this->faker->password()
            ]
        );
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"message":"Account created successfully"');
        $I->seeInRepository(
            User::class,
            [
                'firstName' => $firstName,
                'lastName'  => $lastName,
                'email'     => $emailAddress
            ]
        );
    }

    public function registerWithoutFirstNameAndFail(ApiTester $I) {

        $I->sendPost(
            '/register',
            [
                'lastName'     => $this->faker->lastName(),
                'emailAddress' => $this->faker->email(),
                'password'     => $this->faker->password()
            ]
        );
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"error":"First name is required"');
    }

    public function registerWithoutLastNameAndFail(ApiTester $I) {

        $I->sendPost(
            '/register',
            [
                'firstName'    => $this->faker->firstName(),
                'emailAddress' => $this->faker->email(),
                'password'     => $this->faker->password()
            ]
        );
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"error":"Last name is required"');
    }

    public function registerWithoutEmailAddressAndFail(ApiTester $I) {

        $I->sendPost(
            '/register',
            [
                'firstName' => $this->faker->firstName(),
                'lastName'  => $this->faker->lastName(),
                'password'  => $this->faker->password()
            ]
        );
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"error":"Email address is required"');
    }

    public function registerWithoutPasswordAndFail(ApiTester $I) {

        $I->sendPost(
            '/register',
            [
                'firstName'    => $this->faker->firstName(),
                'lastName'     => $this->faker->lastName(),
                'emailAddress' => $this->faker->email(),
            ]
        );
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"error":"Password is required"');
    }

    public function registerUserAndEnsurePasswordIsHashed(ApiTester $I) {

        $emailAddress = $this->faker->email();
        $password = $this->faker->password();

        $I->sendPost(
            '/register',
            [
                'firstName'    => $this->faker->firstName(),
                'lastName'     => $this->faker->lastName(),
                'emailAddress' => $emailAddress,
                'password'     => $password
            ]
        );

        $user = $I->grabEntityFromRepository(
            User::class,
            [
                'email' => $emailAddress
            ]
        );

        $hasher = $I->grabService('security.user_password_hasher');
        $I->assertTrue($hasher->isPasswordValid($user, $password));
    }

}
