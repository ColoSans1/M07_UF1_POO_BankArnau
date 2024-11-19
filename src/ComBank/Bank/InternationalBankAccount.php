<?php

namespace ComBank\Bank;

use ComBank\Bank\Traits\ApiTrait;  

class InternationalBankAccount extends BankAccount
{
    use ApiTrait;  

    private string $currency; 

    public function __construct(float $balance, string $currency = 'EUR')
    {
        parent::__construct($balance);  
        $this->currency = $currency;
    }

    public function getConvertedCurrency(string $toCurrency = 'USD'): float
    {
        try {
            // Convertimos el saldo actual a la divisa indicada
            return $this->convertCurrency($this->getBalance(), $this->currency, $toCurrency);
        } catch (\Exception $e) {
            throw new \Exception("Error durante la conversión: " . $e->getMessage());
        }
    }
}
