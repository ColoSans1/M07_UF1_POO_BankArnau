<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

 use ComBank\Bank\BankAccount;
 use ComBank\Bank\ApiTrait;
 use ComBank\Bank\NationalBankAccount;
 use ComBank\Bank\InternationalBankAccount;
 use ComBank\OverdraftStrategy\SilverOverdraft;
 use ComBank\Transactions\DepositTransaction;
 use ComBank\Transactions\WithdrawTransaction;
 use ComBank\Exceptions\BankAccountException;
 use ComBank\Exceptions\FailedTransactionException;
 use ComBank\Exceptions\InvalidOverdraftFundsException;
 use ComBank\Exceptions\ZeroAmountException;
 
 require_once 'bootstrap.php';
 
 
 //---[Bank account 1]---/
 // create a new account1 with balance 400
 pl('--------- [Start testing bank account #1, No overdraft] --------');
 try {
 
     $bankAccount1 = new BankAccount(400);
     
     // show balance account
     pl('My Balance: ' . $bankAccount1->getBalance());
 
     // close account
     pl('Closing bank account: closed');
     $bankAccount1->closeAccount();
 
     // reopen account
     pl('Reopening bank account: reopened');
     $bankAccount1->reOpenAccount();
 
     // deposit +150 
     pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
     $bankAccount1->transaction(new DepositTransaction(150.0));
     pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());
 
     // withdrawal -25
     pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
     $bankAccount1->transaction(new WithdrawTransaction(25.0));
     pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());
 
     // withdrawal -600
     pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
     $bankAccount1->transaction(new WithdrawTransaction(600));
 } catch (ZeroAmountException $e) {
     pl($e->getMessage());
 } catch (BankAccountException $e) {
     pl($e->getMessage());
 } catch (FailedTransactionException $e) {
     pl('Error transaction: ' . $e->getMessage());
     pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());
 } catch (ZeroAmountException $e) {
     pl('Error transaction: ' . $e->getMessage());
 } catch (InvalidOverdraftFundsException $e) {
     pl('Error transaction: ' . $e->getMessage());
 }
 
 
 
 //---[Bank account 2]---/
 pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
 try {
 
     $bankAccount2 = new BankAccount(100.0);
     $bankAccount2->applyOverdraft(new SilverOverdraft());
     // show balance account
     pl('My Balance: ' . $bankAccount2->getBalance());
 
     // deposit +100
     pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
     $bankAccount2->transaction(new DepositTransaction(100));
     pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());
 
     // withdrawal -300
     pl('Doing transaction deposit (-300) with current balance ' . $bankAccount2->getBalance());
     $bankAccount2->transaction(new WithdrawTransaction(300));
     pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());
 
     // withdrawal -50
     pl('Doing transaction deposit (50) with current balance ' . $bankAccount2->getBalance());
     $bankAccount2->transaction(new DepositTransaction(50));
     pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());
 
     // withdrawal -120
     pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
     $bankAccount2->transaction(new WithdrawTransaction(120));
 } catch (InvalidArgumentException $e) {
     pl('' . $e->getMessage());
 } catch (FailedTransactionException $e) {
     pl('Error transaction: ' . $e->getMessage());
     pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());
 } catch (ZeroAmountException $e) {
     pl('' . $e->getMessage());
 }
 
 try {
     pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
     $bankAccount2->transaction(new WithdrawTransaction(20));
     pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());
 } catch (InvalidArgumentException $e) {
     pl('' . $e->getMessage());
 } catch (FailedTransactionException $e) {
     pl('Error transaction: ' . $e->getMessage());
     pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());
 } catch (ZeroAmountException $e) {
     pl('' . $e->getMessage());
 }

// ---[Start testing national account (no conversion)]---/
pl('--------- [Start testing national account (no conversion)] --------');

try {
    // Crear una cuenta nacional con un saldo inicial de 500 EUR
    $nationalAccount = new BankAccount(500.0);

    // Mostrar saldo de la cuenta nacional
    pl('Initial balance of national account: ' . $nationalAccount->getBalance());

    // Realizar un depósito de +200 EUR
    pl('Depositing +200 EUR with current balance ' . $nationalAccount->getBalance());
    $nationalAccount->transaction(new DepositTransaction(200.0));
    pl('New balance after deposit (+200 EUR): ' . $nationalAccount->getBalance());

    // Realizar un retiro de -150 EUR
    pl('Withdrawing -150 EUR with current balance ' . $nationalAccount->getBalance());
    $nationalAccount->transaction(new WithdrawTransaction(150.0));
    pl('New balance after withdrawal (-150 EUR): ' . $nationalAccount->getBalance());

    // Intentar realizar un retiro mayor que el saldo disponible, -700 EUR
    pl('Attempting withdrawal of -700 EUR with current balance ' . $nationalAccount->getBalance());
    $nationalAccount->transaction(new WithdrawTransaction(700));

} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
    pl('My balance after failed last transaction: ' . $nationalAccount->getBalance());
} catch (InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

// ---[Start testing national account dollar conversion]---/
pl('--------- [Start testing national account dollar conversion] --------');

try {
    // Crear una cuenta internacional con un saldo de 300 EUR
    $nationalAccount = new InternationalBankAccount(300.0, 1.10);  // Usar la clase que implementa el trait

    pl('Initial balance of national account (EUR): ' . $nationalAccount->getBalance());

    // Convertir el saldo usando el método del trait
    $convertedBalance = $nationalAccount->getConvertedCurrency();  // Este método llama al trait

    pl('Converted balance to USD (using conversion API): ' . $convertedBalance);

    pl('Converted balance (expected 330 USD): ' . $convertedBalance);

} catch (\InvalidArgumentException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
    pl('My balance after failed last transaction: ' . $nationalAccount->getBalance());
} catch (\Exception $e) {
    pl('Unexpected error: ' . $e->getMessage());
}

// ---[Check Gmail verification Test]---/

pl('--------[START GMAIL TEST VALIDATION]---------');
