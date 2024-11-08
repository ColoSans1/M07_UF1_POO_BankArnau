<?php

namespace ComBank\Bank;

use ComBank\Bank\BankAccount;

class Person extends BankAccount
{
    private $name;
    private $idcard;
    private $email;

    public function __construct(string $name, string $idcard, string $email, float $balance = 0.0)
    {
        parent::__construct($balance);

        $this->name = $name;
        $this->idcard = $idcard;
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIdcard(): string
    {
        return $this->idcard;
    }

    public function setIdcard(string $idcard): void
    {
        $this->idcard = $idcard;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function showDetails(): void
    {
        echo "Name: " . $this->name . "\n";
        echo "ID Card: " . $this->idcard . "\n";
        echo "Email: " . $this->email . "\n";
        echo "Account Balance: " . $this->getBalance() . "\n";
    }
}
