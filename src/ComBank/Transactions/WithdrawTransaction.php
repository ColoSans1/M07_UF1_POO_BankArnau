<?php

namespace ComBank\Transactions;


use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Exceptions\FailedTransactionException;

class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface
{

    public function __construct($amount)
    {
        parent::validateAmount($amount);

        $this->amount = $amount;
    }

    public function applyTransaction(BackAccountInterface $account): float
    {


        $newBalance = $account->getBalance() - $this->getAmount();

        if ($newBalance < 0) {
            if ($account->getOverdraft()->getOverdraftFundsAmount() == 0) {
                throw new InvalidOverdraftFundsException("No puedes retirar esta cantidad de dinero, tu limite es 0");
            } else {
                if (!$account->getOverdraft()->isGrantOverdraftFunds(($newBalance)))
                    throw new FailedTransactionException("No puedes retirar esta cantidad de dinero, tu limite es -100");
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