<?php

namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct(float $amount)
    {
        // Validación de la cantidad inicial
        if ($amount <= 0) {
            throw new ZeroAmountException("The amount cannot be zero or negative.");
        }

        parent::validateAmount($amount);

        $this->amount = $amount;
    }

    public function applyTransaction(BackAccountInterface $account): float
    {
        $newBalance = $account->getBalance() - $this->getAmount();

        if ($newBalance < 0) {
            $overdraft = $account->getOverdraft();

            if ($overdraft->getOverdraftFundsAmount() <= 0) {
                throw new InvalidOverdraftFundsException(
                    "You cannot withdraw this amount of money, your overdraft limit is 0."
                );
            }

            // Si excede los límites permitidos del sobregiro
            if (!$overdraft->isGrantOverdraftFunds($newBalance)) {
                throw new FailedTransactionException(
                    "You cannot withdraw this amount of money, your overdraft limit is exceeded."
                );
            }
        }

        $account->setBalance($newBalance);

        return $account->getBalance();
    }

    public function getTransaction(): string
    {
        return "WITHDRAW_TRANSACTION";
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
