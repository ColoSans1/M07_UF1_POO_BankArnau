<?php namespace ComBank\Exceptions;



class ZeroAmountException extends BaseExceptions
{
    protected $errorCode = 101;
    protected $errorLabel = 'ZeroAmountException';
}