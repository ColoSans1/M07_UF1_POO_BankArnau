<?php

namespace ComBank\Bank;

use ComBank\Bank\BankAccount;

class NationalBankAccount extends BankAccount
{

    public function __construct(float $balance)
    {
        parent::__construct($balance);  
    }


    public function getBalance(): float
    {
        return $this->balance;  
    }
}
