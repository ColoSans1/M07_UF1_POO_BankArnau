<?php namespace ComBank\Transactions;

use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Bank\BankAccount;
use ComBank\Exceptions\FailedTransactionException;

class WithdrawTransaction implements BankTransactionInterface
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
        // Obtener el balance actual
        $currentBalance = $account->getBalance();
        
        // Sumar el monto al balance
        $newBalance = $currentBalance + $this->amount;
        
        // Establecer el nuevo balance en el banco
        $account->setBalance($newBalance);
    }
    
    
}
