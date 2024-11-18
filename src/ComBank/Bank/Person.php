<?php

namespace ComBank\Bank;

use ComBank\Bank\ApiTrait;

class Person 
{



    private $name;
    private $idcard;
    private $email;
    private $bankAccount;

    public function __construct(string $name, string $idcard, string $email, float $balance = 0.0)
    {
        $this->name = $name;
        $this->idcard = $idcard;
        $this->email = $email;
        $this->bankAccount = new BankAccount($balance);  
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

 

}
