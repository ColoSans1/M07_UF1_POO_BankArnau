<?php

namespace ComBank\Transactions;

use ComBank\Exceptions\ZeroAmountException; 
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class DepositTransaction extends BaseTransaction implements BankTransactionInterface
{
    protected $amount;

    public function __construct($amount)
    {
        if ($amount <= 0) {
            throw new ZeroAmountException("Amount cannot be zero or negative.");
        }

        parent::validateAmount($amount);
        $this->amount = $amount;
    }

    public function getTransaction(): string
    {
        return "DEPOSIT_TRANSACTION";
    }

    public function applyTransaction(BackAccountInterface $account): float
    {
        $newBalance = $account->getBalance() + $this->getAmount();
        $account->setBalance($newBalance);
        return $account->getBalance();
    }

    public function getTransactionInfo(): string
    {
        return "DEPOSIT_TRANSACTION";
    }

    public function getAmount(): float
    {
        return $this->amount;
    }


}
