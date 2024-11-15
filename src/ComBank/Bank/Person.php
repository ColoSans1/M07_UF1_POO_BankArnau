<?php

namespace ComBank\Bank;

use ComBank\Bank\ApiTrait;

class Person 
{
    use ApiTrait;

    private $name;
    private $idcard;
    private $email;
    private $bankAccount;

    public function __construct(string $name, string $idcard, string $email, float $balance = 0.0)
    {
        $this->name = $name;
        $this->idcard = $idcard;
        $this->email = $email;

        // Validar correo al crear la persona
        $this->validateEmail($email);

        $this->bankAccount = new BankAccount($balance);  
    }

    private function validateEmail(string $email): void
    {
        // Llamar al servicio de verificación de Gmail
        $response = $this->verificationGmail($email);

        // Verificar formato válido
        if (empty($response['is_valid_format']) || !$response['is_valid_format']['value']) {
            throw new \Exception("Invalid email format: $email");
        }

        // Verificar si el correo es no entregable
        if ($response['status'] === 'undeliverable') {
            throw new \Exception("Undeliverable email: $email");
        }
    }

    // Métodos de acceso para las propiedades
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
        // Validar el nuevo correo antes de asignarlo
        $this->validateEmail($email);
        $this->email = $email;
    }

    // Mostrar detalles de la persona
    public function showDetails(): void
    {
        echo "Name: " . $this->name . "\n";
        echo "ID Card: " . $this->idcard . "\n";
        echo "Email: " . $this->email . "\n";
        echo "Account Balance: " . $this->bankAccount->getBalance() . "\n";
    }
}
