<?php namespace ComBank\Bank;

use ComBank\Exceptions\BankAccountException;
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

class BankAccount implements BackAccountInterface
{
   // Propiedades de la clase
   private $balance;
   private $isOpen; // Esta es la propiedad que indica si la cuenta está abierta
   private $overdraft;

    public function __construct(float $initialBalance = 0, OverdraftInterface $overdraft = null)
    {
        $this->balance = $initialBalance;
        $this->isOpen = true;  // Asegúrate de que la cuenta esté abierta al crearla
        $this->overdraft = $overdraft ?: new NoOverdraft();
    }

    public function transaction(BankTransactionInterface $transaction): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("You cannot perform a transaction in a closed account.");
        }
        // Lógica de la transacción
        $this->balance = $transaction->applyTransaction($this);
    }

    public function openAccount(): void
    {
        if ($this->isOpen) {
            throw new BankAccountException("The account is already open.");
        }
        $this->isOpen = true; // Abrir la cuenta
    }

    public function isOpen(): bool
    {
        return $this->isOpen; // Devuelve el estado de la cuenta
    }

    public function reopenAccount(): void
    {
        if ($this->isOpen) {
            throw new BankAccountException("The account is already open.");
        }
        $this->isOpen = true; // Reabrir la cuenta
    }

    public function closeAccount(): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("The account is already closed.");
        }
        $this->isOpen = false; // Cerrar la cuenta
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

    public function applyOverdraft(OverdraftInterface $overdraft): void
    {
        $this->overdraft = $overdraft; // Aplicar el sobregiro
    }

    public function setBalance($newBalance): void
    {
        $this->balance = $newBalance; // Establecer el nuevo saldo
    }
}
