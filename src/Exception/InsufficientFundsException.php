<?php

namespace App\Exception;

use Exception;

class InsufficientFundsException extends Exception {

    public function __construct() {

        parent::__construct("Insufficient funds available to complete this request");
    }
}