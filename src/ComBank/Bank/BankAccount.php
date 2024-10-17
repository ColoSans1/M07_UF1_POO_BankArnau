<?php namespace ComBank\Bank;

use ComBank\Exceptions\BankAccountException;
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\OverdraftStrategy\NoOverdraft;


class BankAccount implements BackAccountInterface
{
   // Propiedades de la clase
   private $balance;
   private $isOpen; 
   private $overdraft;

    public function __construct(float $initialBalance = 0, OverdraftInterface $overdraft = null)
    {
        $this->balance = $initialBalance;
        $this->isOpen = true;  
        $this->overdraft = $overdraft ?: new NoOverdraft();
    }

    public function transaction(BankTransactionInterface $transaction): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("You cannot perform a transaction in a closed account.");
        }
        // Lógica de la transacción
        $this->balance = $transaction->applyTransaction($this);
    }

    public function openAccount(): bool
    {
        if ($this->isOpen) {
            return true; 
        }
        $this->isOpen = true; 
        return $this->isOpen; 
    }

    public function isOpen(): bool
    {
        return $this->isOpen; 
    }

    public function reopenAccount(): bool
    {
        if ($this->isOpen) {
            throw new \ComBank\Exceptions\BankAccountException("The account is already open.");
        }
        $this->isOpen = true; // Reabrir la cuenta si estaba cerrada
        return $this->isOpen; // Devolver el estado
    }
    
    
    public function closeAccount(): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("The account is already closed.");
        }
        $this->isOpen = false; 
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

    public function applyOverdraft(OverdraftInterface $overdraft): void
    {
        $this->overdraft = $overdraft;
    }

    public function setBalance($newBalance): void
    {
        $this->balance = $newBalance; 
    }
}
