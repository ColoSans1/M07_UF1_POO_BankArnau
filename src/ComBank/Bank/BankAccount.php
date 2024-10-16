<?php namespace ComBank\Bank;

use ComBank\Exceptions\BankAccountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BackAccountInterface
{
    private float $balance;
    private bool $isOpen; // Definición de la propiedad isOpen
    private OverdraftInterface $overdraft;

    public function __construct(float $initialBalance = 0, OverdraftInterface $overdraft = null)
    {
        $this->balance = $initialBalance;
        $this->isOpen = true; // Asegúrate de que la cuenta esté abierta al crearla
        $this->overdraft = $overdraft ?: new NoOverdraft();
    }

    public function transaction(BankTransactionInterface $transaction): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("You cannot perform a transaction in a closed account.");
        } else {
            // Verifica que el método applyTransaction exista y se utilice correctamente
            $this->balance = $transaction->applyTransaction($this);
        }
    }

    public function openAccount(): bool
    {
        if ($this->isOpen) {
            return false; // La cuenta ya está abierta
        }
        $this->isOpen = true; // Abrir la cuenta
        return true; // Indica que se ha abierto la cuenta
    }

    public function reopenAccount(): void
    {
        if ($this->isOpen) {
            throw new BankAccountException("The account is already open."); // Lanza la excepción si ya está abierta
        }
        $this->isOpen = true; // Reabrir la cuenta
    }

    public function closeAccount(): void
    {
        if (!$this->isOpen) {
            throw new BankAccountException("The account is already closed."); // Lanza la excepción si ya está cerrada
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
        $this->overdraft = $overdraft;
    }

    public function setBalance($newBalance)
    {
        $this->balance = $newBalance;
    }
}
