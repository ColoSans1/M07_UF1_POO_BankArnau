<?php

namespace ComBank\Bank;

class InternationalBankAccount extends BankAccount
{
    private float $conversionRate;

    public function __construct(float $balance, float $conversionRate)
    {
        parent::__construct($balance);  
        $this->conversionRate = $conversionRate;  
    }

    public function getConvertedBalance(): float
    {
        return $this->getBalance() * $this->conversionRate;  
    }
    public function getConvertedCurrency(): string
    {
        return "USD";  
    }
}
