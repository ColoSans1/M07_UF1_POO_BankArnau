<?php

namespace ComBank\Bank;

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
use Exception;

class BankAccount implements BackAccountInterface
{
    use AmountValidationTrait;

    private  $balance;

    private  $status;

    private  $overdraft;

    function __construct(float $balance = 100)
    {
        $this->validateAmount($balance);
        
        $this->balance = $balance;
        $this->status = true;
        $this->overdraft =  new NoOverdraft();
    }

    public function transaction(BankTransactionInterface $transaction): void
    {
        if (!isset($this->status) || !$this->status) throw new BankAccountException("La cuenta no esta abierta");
        $transaction->applyTransaction($this);
    }

    public function openAccount():bool
    {
        if (!isset($this->status)) $this->status = true;

        return $this->status;
    }

    public function reopenAccount(): void
    {
        if (!isset($this->status)) throw new BankAccountException('La cuenta no ha sido nunca abierta antes');
        if ($this->status) throw new BankAccountException('La cuenta ya esta abierta');

        $this->status = true;
    }

    public function closeAccount(): void
    {
        if (!isset($this->status)) throw new BankAccountException('La cuenta no ha sido nunca abierta antes');
        if (!$this->status) throw new BankAccountException('La cuenta ya esta cerrada');

        $this->status = false;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

    public function applyOverdraft(OverdraftInterface $overdraft): void {
        $this->overdraft = $overdraft;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }


    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
}