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
use PhpParser\Node\Expr\StaticCall;

class BankAccount
{
    private $balance;
    private $isOpen;
    private $overdraft; 

    public function __construct(float $initialBalance = 0)
    {
        $this->balance = $initialBalance;  // Asignar el saldo inicial
        $this->isOpen = true;  // Por defecto, la cuenta estará abierta
    }

    // Método para procesar las transacciones
    public function transaction($transaction): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("The account is closed.");
        }

        if ($transaction instanceof DepositTransaction) {
            $this->balance += $transaction->getAmount();  // Sumar la cantidad depositada
        } elseif ($transaction instanceof WithdrawTransaction) {
            // Verificar si tiene fondos suficientes o si tiene descubierto
            $amount = $transaction->getAmount();
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
        $this->isOpen = true;
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
        $this->isOpen = true;
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











