<?php

namespace ComBank\Bank;

use ComBank\Bank\ApiTrait;  // Asegúrate de incluir el trait

class InternationalBankAccount extends BankAccount
{
    use ApiTrait;  // Usa el trait aquí

    private float $conversionRate;

    public function __construct(float $balance, float $conversionRate)
    {
        parent::__construct($balance);  
        $this->conversionRate = $conversionRate;  
    }

    // Método para obtener la moneda convertida
    public function getConvertedCurrency(): float
    {
        return $this->convertCurrency($this->getBalance(), 'EUR', 'USD');
    }

    // Método para obtener el balance convertido
    public function getConvertedBalance(): float
    {
        return $this->getBalance() * $this->conversionRate;
    }

    // Método para obtener el nombre de la moneda convertida
    public function getConvertedCurrencyCode(): string
    {
        return 'USD';
    }
}
