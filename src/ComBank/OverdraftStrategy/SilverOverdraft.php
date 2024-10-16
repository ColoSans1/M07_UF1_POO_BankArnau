<?php namespace ComBank\OverdraftStrategy;

namespace ComBank\OverdraftStrategy;

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

/**
 * Class SilverOverdraft
 * @package ComBank\OverdraftStrategy
 * @description: Grant 100.00 overdraft funds.
 */
class SilverOverdraft implements OverdraftInterface
{
    private float $limit = 100.00;  // Límite de descubierto permitido
    private float $overdraftBalance = 0.0;  // Saldo de descubierto actual

    public function canWithdraw(float $amount): bool
    {
        // Permitir retiros si hay suficientes fondos o dentro del límite de descubierto
        return ($amount <= 0) || ($amount <= $this->getAvailableFunds());
    }

    public function getLimit(): float
    {
        return $this->limit; // Retorna el límite de descubierto
    }

    public function getOverdraftBalance(): float
    {
        return $this->overdraftBalance; // Retorna el saldo de descubierto actual
    }

    public function applyOverdraft(float $amount): void
    {
        // Permitir aplicar descubierto hasta el límite
        if ($this->overdraftBalance + $amount > $this->limit) {
            throw new \ComBank\Exceptions\InvalidOverdraftFundsException("Exceeded overdraft limit.");
        }

        $this->overdraftBalance += $amount; // Aumenta el saldo de descubierto
    }

    public function clearOverdraft(): void
    {
        $this->overdraftBalance = 0.0; // Restablecer el saldo de descubierto
    }

    /**
     * Calcula los fondos disponibles considerando el saldo actual de la cuenta y el límite de descubierto.
     */
    private function getAvailableFunds(): float
    {
        return $this->limit - $this->overdraftBalance;
    }
}
