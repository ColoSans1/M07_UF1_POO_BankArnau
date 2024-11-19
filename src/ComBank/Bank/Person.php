<?php
namespace ComBank\Person;

use ComBank\Person\Exceptions\InvalidEmailException;

use Combank\Bank\ApiTrait;
class Person
{
    private string $name;
    private string $idCard;
    private string $email;


    public function __construct($name, $idCard, $email)
    {
         
        if ($this->validateEmail($email)) {
            $this->name = $name;
            $this->idCard = $idCard;
            $this->email = $email;
        } 
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
        $this->email = $email;

        return $this;
    }
}