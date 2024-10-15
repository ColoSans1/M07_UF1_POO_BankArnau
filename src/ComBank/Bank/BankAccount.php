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
use PhpParser\Node\Expr\StaticCall;

class BankAccount
{
    private $balance;
    private $isOpen;
    private $overdraft; 

    public function __construct()
    {
        $this->balance = 0.0;
        $this->isOpen = false;
        $this->overdraft = null; // No overdraft by default
    }

    public function openAccount(): bool
    {
        if ($this->isOpen) {
            throw new BankAccountException("The account is already open.");
        }
        $this->isOpen = true;
        return true;
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