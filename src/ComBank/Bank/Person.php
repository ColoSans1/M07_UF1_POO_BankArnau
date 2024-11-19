<?php
namespace ComBank\Bank;

use ComBank\Bank\Traits\ApiTrait;

class Person
{
    use ApiTrait; 

    private string $name;
    private string $idCard;
    private string $email;

    public function __construct($name, $idCard, $email)
    {
        $validation = $this->validateEmail($email);
        if (!$validation['isValid'] || $validation['deliverability'] !== 'DELIVERABLE')

        $this->name = $name;
        $this->idCard = $idCard;
        $this->email = $email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getIdCard()
    {
        return $this->idCard;
    }

    public function setIdCard($idCard)
    {
        $this->idCard = $idCard;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        // Validar el email antes de asignarlo
        $validation = $this->validateEmail($email);
        if (!$validation['isValid'] || $validation['deliverability'] !== 'DELIVERABLE')
        $this->email = $email;
        return $this;
    }
}
