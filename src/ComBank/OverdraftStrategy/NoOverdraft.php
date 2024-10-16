<?php namespace ComBank\OverdraftStrategy;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 12:27 PM
 */

 use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
 
 /**
  * Class NoOverdraft
  * @package ComBank\OverdraftStrategy
  */
 class NoOverdraft implements OverdraftInterface
 {
     private float $limit = 0.0;  // No hay límite de descubierto
 
     public function canWithdraw(float $amount): bool
     {
         // Solo permite retiros si el monto es menor o igual al saldo.
         return $amount <= 0; // En una cuenta sin descubierto, el monto a retirar debe ser positivo
     }
 
     public function getLimit(): float
     {
         return $this->limit; // Devuelve 0, ya que no hay descubierto permitido
     }
 
     public function getOverdraftBalance(): float
     {
         return 0.0; // No hay descubierto
     }
 
     public function applyOverdraft(float $amount): void
     {
         // No se permite aplicar descubierto
         throw new \ComBank\Exceptions\InvalidOverdraftFundsException("Overdraft not allowed.");
     }
 
     public function clearOverdraft(): void
     {
         // No hay nada que limpiar en una cuenta sin descubierto
     }
 }
 