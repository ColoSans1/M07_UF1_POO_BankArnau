<?php

use PHPUnit\Framework\TestCase;
use ComBank\Bank\BankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\OverdraftStrategy\NoOverdraft;


class BankAccountTest extends TestCase
{
    public function testInitialBalanceIsSetCorrectly(): void
    {
        $account = new BankAccount(100.0);
        $this->assertEqualsWithDelta(100.0, $account->getBalance(), 0.001);
    }

    public function testDepositTransaction(): void
    {
        $bankAccount = new BankAccount(200.0);
        $bankAccount->transaction(new DepositTransaction(30.0));
        $this->assertEqualsWithDelta(230.0, $bankAccount->getBalance(), 0.001);
    }

    public function testWithdrawTransaction(): void
    {
        $bankAccount = new BankAccount(200.0);
        $bankAccount->transaction(new WithdrawTransaction(150.0));
        $this->assertEqualsWithDelta(50.0, $bankAccount->getBalance(), 0.001);
    }

    public function testCannotReopenOpenAccount(): void
    {
        $this->expectException(BankAccountException::class);

        $account = new BankAccount(100.0);
        $account->reopenAccount(); 
    }

    public function testCanCloseAndReopenAccount(): void
    {
        $account = new BankAccount(100.0);
        $account->closeAccount();
        $this->assertFalse($account->openAccount());

        $account->reopenAccount();
        $this->assertTrue($account->openAccount());
    }                                                                                 

    public function testWithdrawWithOverdraft(): void
    {
        $bankAccount = new BankAccount(250.0);
        $bankAccount->applyOverdraft(new SilverOverdraft());
        $bankAccount->transaction(new WithdrawTransaction(300.0));

        $this->assertEqualsWithDelta(-50.0, $bankAccount->getBalance(), 0.001);
    }

    // Test a failed transaction due to overdraft refusal
    public function testFailedTransactionWithOverdraft(): void
    {
        $this->expectException(FailedTransactionException::class);        

        $bankAccount = new BankAccount(100.0);
        $bankAccount->applyOverdraft(new SilverOverdraft());        
        $bankAccount->transaction(new WithdrawTransaction(201.0));
    }

    public function testTransactionAfterAccountClosed(): void
    {
        $this->expectException(BankAccountException::class);

        $bankAccount = new BankAccount(100.0);
        $bankAccount->closeAccount();

        $bankAccount->transaction(new DepositTransaction(50.0));  
    }
}
