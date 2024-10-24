<?php namespace ComBank\Bank\Contracts;


use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

interface BackAccountInterface
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

    public function getOverdraft():OverdraftInterface;

    public function transaction(BankTransactionInterface $transaction): void;

    public function applyOverdraft(OverdraftInterface $overdraft): void;

    public function reopenAccount():void;

    public function getBalance():float;

    public function openAccount():bool;

    public function closeAccount():void;


    public function setBalance(float $balance): void;
}