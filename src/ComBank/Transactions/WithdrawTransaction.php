<?php namespace ComBank\Transactions;

use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Bank\BankAccount;
use ComBank\Exceptions\FailedTransactionException;

class WithdrawTransaction implements BackAccountInterface
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
    public function applyTransaction(BackAccountInterface $account): float
    {
        $currentBalance = $account->getBalance();
        $newBalance = $currentBalance - $this->amount; // Resta el monto, ya que es un retiro
        $account->setBalance($newBalance);
        
        return $newBalance; // Devuelve el nuevo balance como float
    }
    
    
}
