<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\ZeroAmountException;

require_once 'bootstrap.php';

//---[Bank Account Example #1]---/
pl('=== [Initiating transactions on bank account #1 with no overdraft] ===');

try {
    $account1 = new BankAccount(400);

    // Display initial balance
    pl('Account opened with initial balance: ' . $account1->getBalance());

    // Deposit of 150
    pl('Attempting deposit: +150; Current balance: ' . $account1->getBalance());
    $account1->transaction(new DepositTransaction(150));
    pl('Updated balance after deposit: ' . $account1->getBalance());

    // Withdraw 25
    pl('Attempting withdrawal: -25; Balance before: ' . $account1->getBalance());
    $account1->transaction(new WithdrawTransaction(25));
    pl('Balance after withdrawal: ' . $account1->getBalance());

    // Withdraw 600
    pl('Attempting withdrawal: -600; Balance before: ' . $account1->getBalance());
    $account1->transaction(new WithdrawTransaction(600));

} catch (ZeroAmountException | BankAccountException | FailedTransactionException $e) {
    pl('Transaction error: ' . $e->getMessage());
}

// Final balance for account 1
pl('Final balance after transactions for account #1: ' . $account1->getBalance());

//---[Bank Account Example #2]---/
pl('=== [Initiating transactions on bank account #2 with Silver overdraft (100 funds)] ===');

try {
    $account2 = new BankAccount(100.0);

    // Apply overdraft limit before transactions
    $account2->applyOverdraft(new SilverOverdraft());

    // Initial balance display
    pl('Starting balance for account #2: ' . $account2->getBalance());

    // Deposit 100
    pl('Attempting deposit: +100; Current balance: ' . $account2->getBalance());
    $account2->transaction(new DepositTransaction(100));
    pl('Balance after deposit: ' . $account2->getBalance());

    // Withdraw 300
    pl('Attempting withdrawal: -300; Balance before: ' . $account2->getBalance());
    $account2->transaction(new WithdrawTransaction(300));
    pl('Balance after withdrawal with overdraft: ' . $account2->getBalance());

    // Deposit 50
    pl('Attempting deposit: +50; Current balance: ' . $account2->getBalance());
    $account2->transaction(new DepositTransaction(50));
    pl('Balance after deposit: ' . $account2->getBalance());

    // Withdraw 120
    pl('Attempting withdrawal: -120; Balance before: ' . $account2->getBalance());
    $account2->transaction(new WithdrawTransaction(120));
    
} catch (FailedTransactionException $e) {
    pl('Transaction error: ' . $e->getMessage());
}

// Additional transaction check
try {
    pl('Attempting withdrawal of 20; Current balance: ' . $account2->getBalance());
    $account2->transaction(new WithdrawTransaction(20));
    pl('Final balance for account #2: ' . $account2->getBalance());
} catch (BankAccountException | FailedTransactionException $e) {
    pl('Transaction error: ' . $e->getMessage());
}
