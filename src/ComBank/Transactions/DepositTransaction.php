<?php

namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class DepositTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct($amount)
    {
        parent::validateAmount($amount);

        $this->amount = $amount;
    }

    public function applyTransaction(BackAccountInterface $account): float
    {

        $newBalance = $account->getBalance() + $this->getAmount();

        $account->setBalance($newBalance);

        return $account->getBalance();
    }

    public function getTransaction(): string
    {
        return "DEPOSIT_TRANSACTION";
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
    
}