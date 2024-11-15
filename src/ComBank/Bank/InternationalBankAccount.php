<?php

namespace ComBank\Bank;

use ComBank\Bank\ApiTrait;

class InternationalBankAccount extends BankAccount
{
    use ApiTrait; 

    private float $conversionRate;

    public function __construct(float $balance, float $conversionRate = 1.0)
    {
        parent::__construct($balance);  
        $this->conversionRate = $conversionRate;  
    }

    public function getConvertedCurrency(string $from = 'EUR', string $to = 'USD'): float
    {
        return $this->convertCurrency($this->getBalance(), $from, $to);
    }

    public function getConvertedBalance(): float
    {
        return $this->getBalance() * $this->conversionRate;
    }

    public function getConvertedCurrencyCode(): string
    {
        return 'USD';
    }
}
