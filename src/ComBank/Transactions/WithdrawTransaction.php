<?php

namespace ComBank\Transactions;


use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\ZeroAmountException;


class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct($amount)
    {
        // Validar si la cantidad es 0
        if ($amount <= 0  ) {
            throw new ZeroAmountException("The amount cannot be zero.");
        }
        // Validar el monto utilizando la validación del padre
        parent::validateAmount($amount);

        $this->amount = $amount;
    }

    public function applyTransaction(BackAccountInterface $account): float
    {
        $newBalance = $account->getBalance() - $this->getAmount();

        if ($newBalance < 0) {
            if ($account->getOverdraft()->getOverdraftFundsAmount() == 0) {
                throw new InvalidOverdraftFundsException("You cannot withdraw this amount of money, your limit is 0");
            } else {
                if (!$account->getOverdraft()->isGrantOverdraftFunds($newBalance)) {
                    throw new FailedTransactionException("You cannot withdraw this amount of money, your limit is -100");
                }
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