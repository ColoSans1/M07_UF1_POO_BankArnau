<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class WithdrawTransaction 
{
    
        private float $amount;
    
        public function __construct(float $amount)
        {
            if ($amount <= 0) {
                throw new \ComBank\Exceptions\ZeroAmountException("The amount must be greater than zero.");
            }
            $this->amount = $amount;
        }
    
        public function getAmount(): float
        {
            return $this->amount;
        }

   
}
