<?php

namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\Entity\User;
use Codeception\Module;
use Faker\Factory;

class Api extends Module {

    public function grabUser(bool $isAuthenticated = false)
    : User {

        $faker = Factory::create();
        $email = $faker->email();
        $IDoctrine = $this->getModule('Doctrine2');
        $IDoctrine->haveInRepository(
            User::class,
            [
                'firstName' => $faker->firstName(),
                'lastName'  => $faker->lastName(),
                'email'     => $email,
                'password'  => $faker->password(),
                'apiToken'  => $isAuthenticated ?
                    bin2hex(random_bytes(32)) :
                    null,
            ]
        );

        return $IDoctrine->grabEntityFromRepository(
            User::class,
            ['email' => $email]
        );
    }
}