<?php namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;

class BankAccount
{
    private $balance;
    private $isOpen;
    private $overdraft; 

    public function __construct(float $initialBalance = 0, OverdraftInterface $overdraft = null)
    {
        $this->balance = $initialBalance;  
        $this->isOpen = true;  
        $this->overdraft = $overdraft ?: new NoOverdraft(); // Inicializa con NoOverdraft si no se proporciona
    }

    public function transaction($transaction): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("The account is closed.");
        }

        if ($transaction instanceof DepositTransaction) {
            $this->balance += $transaction->getAmount();  
        } elseif ($transaction instanceof WithdrawTransaction) {
            $amount = $transaction->getAmount();
            // Verifica si el saldo es suficiente o si se permite el sobregiro
            if ($this->balance - $amount < 0 && !$this->overdraft) {
                throw new FailedTransactionException("Insufficient funds.");
            }
            $this->balance -= $amount;
        } else {
            throw new BankAccountException("Invalid transaction type.");
        }
    }

    public function openAccount(): void
    {
        if ($this->isOpen) {
            throw new BankAccountException("The account is already open.");
        }
        $this->isOpen = true; // Abrir la cuenta
    }

    public function closeAccount(): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("The account is already closed.");
        }
        $this->isOpen = false;
    }

    public function reopenAccount(): void
    {
        if ($this->isOpen) {
            throw new BankAccountException("The account is already open.");
        }
        $this->isOpen = true; // Reabrir la cuenta
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
    
    public function getOverdraft(): ?OverdraftInterface
    {
        return $this->overdraft;
    }

    public function applyOverdraft(OverdraftInterface $overdraft): void
    {
        $this->overdraft = $overdraft;
    }

    public function setBalance(float $balance): void
    {
        if ($balance < 0) {
            throw new BankAccountException("Cannot set a negative balance.");
        }
        $this->balance = $balance;
    }
}
