<?php namespace ComBank\Transactions;

use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Bank\BankAccount;

class WithdrawTransaction implements BankTransactionInterface
{
    private float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function applyTransaction(BankAccount $account): float
    {
        // Lógica para aplicar la transacción
        $currentBalance = $account->getBalance();
        if ($currentBalance - $this->amount < 0) {
        }
        // Resta el monto al balance
        $newBalance = $currentBalance - $this->amount;
        $account->setBalance($newBalance);
        return $newBalance; // Devuelve el nuevo balance
    }

    public function testWithdrawTransaction(): void
{
    $bankAccount = new BankAccount(200.0);
    $withdrawTransaction = new WithdrawTransaction(150.0);
    
    // Aplicar la transacción de retiro
    $bankAccount->transaction($withdrawTransaction);

    // Comprobar que el saldo se actualiza correctamente
    $this->assertEqualsWithDelta(50.0, $bankAccount->getBalance(), 0.001);
}

}
