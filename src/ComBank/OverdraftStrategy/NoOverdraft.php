<?php namespace ComBank\OverdraftStrategy;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 12:27 PM
 */

 use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
 
 class NoOverdraft implements OverdraftInterface
 {
     private float $limit = 0.0; 
 
     public function canWithdraw(float $amount): bool
     {
         return $amount <= $this->getLimit() && $amount <= 0;
     }
     
 
     public function getLimit(): float
     {
         return $this->limit; 
     }

     public function getOverdraftFundsAmount(): float
     {
         return 0.0; 
     }
 
     public function getOverdraftBalance(): float
     {
         return 0.0; 
     }
 
     public function applyOverdraft(float $amount): void
     {
         throw new \ComBank\Exceptions\InvalidOverdraftFundsException("Overdraft not allowed.");
     }
 
     public function clearOverdraft(): void
     {

    }

    public function isGrantOverdraftFunds(float $amount): bool
    {
        return false;
    }
 }
 