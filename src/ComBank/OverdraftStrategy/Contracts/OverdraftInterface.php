<?php namespace ComBank\OverdraftStrategy\Contracts;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:44 PM
 */

 /**
  * Interface OverdraftInterface
  * @package ComBank\OverdraftStrategy\Contracts
  */
 interface OverdraftInterface
 {
     /**
      * Verifica si se puede realizar una transacción dada la cantidad.
      *
      * @param float $amount
      * @return bool
      */
     public function canWithdraw(float $amount): bool;
 
     /**
      * Devuelve el límite de descubierto permitido.
      *
      * @return float
      */
     public function getLimit(): float;
 
     /**
      * Devuelve el saldo actual de descubierto.
      *
      * @return float
      */
     public function getOverdraftBalance(): float;
 
     /**
      * Aplica un descubierto al saldo.
      *
      * @param float $amount
      * @return void
      */
     public function applyOverdraft(float $amount): void;
 
     /**
      * Elimina el descubierto.
      *
      * @return void
      */
     public function clearOverdraft(): void;

 }
 