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


//---[Bank account 1]---/
pl('--------- [Start testing bank account #1, No overdraft] --------');

try {
    // Create a new BankAccount instance with a starting balance of 400
    $bankAccount1 = new BankAccount(400);
    
    // Open the account
    $bankAccount1->openAccount();
    
    // Show initial balance
    pl('Initial balance: ' . $bankAccount1->getBalance());

    // Deposit +150
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150)); // Using the transaction class
    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());

    // Withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25)); // Using the transaction class
    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // Withdrawal -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600)); // Using the transaction class

} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

// Show final balance after attempts
pl('My balance after all transactions: ' . $bankAccount1->getBalance());



//---[Bank account 2]---/
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {
    
    // show balance account
   
    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    
    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());

    // withdrawal -300
    pl('Doing transaction deposit (-300) with current balance ' . $bankAccount2->getBalance());
   
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction deposit (-50) with current balance ' . $bankAccount2->getBalance());
    
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

try {
    pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
    
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());

try {
   
} catch (BankAccountException $e) {
    pl($e->getMessage());
}
