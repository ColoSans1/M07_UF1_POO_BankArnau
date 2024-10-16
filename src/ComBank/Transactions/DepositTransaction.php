<?php namespace ComBank\Transactions;

use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Bank\BankAccount;

class DepositTransaction implements BankTransactionInterface
{
    private float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function applyTransaction(BankAccount $account): void
    {
        $account->transaction($this); // Llama al método de transacción de BankAccount
    }
}
