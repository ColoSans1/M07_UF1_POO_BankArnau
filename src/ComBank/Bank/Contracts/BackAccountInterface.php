<?php namespace ComBank\Bank\Contracts;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:26 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

interface BackAccountInterface
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

   public function transaction(BankTransactionInterface $transaction);

   public function openAccount ();

   public function reopenAccount ();

   public function closeAccount ();

   public function getBalance ();

   public function getOverdraft();

   public function applyOverdraft(OverdraftInterface $overdraft);

   public function setBalance($newBalance);
}