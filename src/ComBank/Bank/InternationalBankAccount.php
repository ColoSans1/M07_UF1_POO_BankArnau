<?php

namespace ComBank\Bank;

use ComBank\Bank\ApiTrait;


namespace ComBank\Bank;

class InternationalBankAccount extends BankAccount
{
    private $conversionRate;

    public function __construct($balance, $conversionRate)
    {
        parent::__construct($balance);
        $this->conversionRate = $conversionRate;
    }

    // Método para obtener el saldo convertido
    public function getConvertedCurrency()
    {
        return $this->getBalance() * $this->conversionRate;
    }
}
