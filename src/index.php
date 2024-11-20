<?php

use ComBank\Bank\BankAccount;
use ComBank\Bank\NationalBankAccount;
use ComBank\Bank\InternationalBankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Bank\Person;
use ComBank\Bank\trait\ApiTrait;

require_once 'bootstrap.php';


//---[Bank account 1]---/
pl('--------- [Start testing bank account #1, No overdraft] --------');
try {
    $bankAccount1 = new BankAccount(400);
    
    // show balance account
    pl('My Balance: ' . $bankAccount1->getBalance());

    // close account
    pl('Closing bank account: closed');
    $bankAccount1->closeAccount();

    // reopen account
    pl('Reopening bank account: reopened');
    $bankAccount1->reOpenAccount();

    // deposit +150 
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150.0));
    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());

    // withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25.0));
    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // withdrawal -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600));

} catch (ZeroAmountException | BankAccountException | FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error: ' . $e->getMessage());
}

//---[Bank account 2]---/
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {
    $bankAccount2 = new BankAccount(100.0);
    $bankAccount2->applyOverdraft(new SilverOverdraft());
    // show balance account
    pl('My Balance: ' . $bankAccount2->getBalance());

    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100));
    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());

    // withdrawal -300
    pl('Doing transaction withdrawal (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300));
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction deposit (50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(50));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120));
} catch (ZeroAmountException | FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error: ' . $e->getMessage());
}

pl('--------- [Start testing national account (no conversion)] --------');
try {
    // Crear una cuenta nacional con un saldo inicial de 500 EUR
    $nationalAccount = new BankAccount(500.0);

    // Mostrar saldo de la cuenta nacional
    pl('Initial balance of national account: ' . $nationalAccount->getBalance());

    // Realizar un depósito de +200 EUR
    pl('Depositing +200 EUR with current balance ' . $nationalAccount->getBalance());
    $nationalAccount->transaction(new DepositTransaction(200.0));
    pl('New balance after deposit (+200 EUR): ' . $nationalAccount->getBalance());

    // Realizar un retiro de -150 EUR
    pl('Withdrawing -150 EUR with current balance ' . $nationalAccount->getBalance());
    $nationalAccount->transaction(new WithdrawTransaction(150.0));
    pl('New balance after withdrawal (-150 EUR): ' . $nationalAccount->getBalance());

    // Intentar realizar un retiro mayor que el saldo disponible, -700 EUR
    pl('Attempting withdrawal of -700 EUR with current balance ' . $nationalAccount->getBalance());
    $nationalAccount->transaction(new WithdrawTransaction(700));

} catch (ZeroAmountException | BankAccountException | FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error: ' . $e->getMessage());
}

//---[Start testing international account dollar conversion]---/
pl('--------- [Start testing international account dollar conversion] --------');
try {
    // Crear una cuenta internacional con saldo en EUR y convertirlo a USD (por ejemplo)
    $nationalAccount = new InternationalBankAccount(400.0, 'EUR'); // Saldo en EUR, moneda base

    pl('Initial balance of national account (EUR): ' . $nationalAccount->getBalance());

    // Convertir a USD usando la API de conversión
    $convertedBalance = $nationalAccount->getConvertedCurrency('USD'); // Convertir a USD
    pl('Converted balance to USD (using conversion API): ' . $convertedBalance);

} catch (\InvalidArgumentException | FailedTransactionException | \Exception $e) {
    pl('Error: ' . $e->getMessage());
}

//--- Test Email Validation --- 
pl('--------- [Test Email Validation] --------');
try {
    $person = new Person('Arnau Colominas', '12345', 'emailvalidation.abstractapi.com');
    pl('Valid email: ' . $person->getEmail());
    $response = $person->validateEmail($person->getEmail());
    if ($response['isValid'] && $response['deliverability'] === 'DELIVERABLE') {
        pl('Email is valid and deliverable.');
    } else {
        pl('Email is invalid or undeliverable.');
    }
} catch (Exception $e) {
    pl($e->getMessage());
}

// --------- [Prueba 6: Depósito permitido por funcionalidad antifraude] --------
pl('--------- [Prueba 6: Depósito permitido por funcionalidad antifraude] --------');
try {
    // Crear una cuenta bancaria con un saldo inicial de 500.0
    $bankAccount = new BankAccount(500.0);  // Balance inicial
    pl('Saldo antes del depósito: ' . $bankAccount->getBalance());
    
    // Simula un depósito de 5000 (permitido según la API)
    $depositTransaction = new DepositTransaction(5000); 
    $bankAccount->transaction($depositTransaction);
    pl('Saldo después del depósito: ' . $bankAccount->getBalance());  // Debe ser 5000 + 500 = 5500

} catch (Exception $e) {
    // En caso de error, se captura y muestra el mensaje
    pl($e->getMessage());
}

// --------- [Prueba 7: Verificar depósito bloqueado por funcionalidad antifraude] --------
pl('--------- [Prueba 7: Verificar depósito bloqueado por funcionalidad antifraude] --------');
try {
    // Crear una cuenta bancaria con un saldo inicial de 500.0
    $bankAccount = new BankAccount(500.0);  // Balance inicial
    pl('Saldo antes del depósito: ' . $bankAccount->getBalance());
    
    // Simula un depósito de 20000 (bloqueado por la API debido a fraude)
    $depositTransaction = new DepositTransaction(20000); 
    $bankAccount->transaction($depositTransaction);  // Este depósito debe ser bloqueado por la API
    pl('Saldo después del depósito: ' . $bankAccount->getBalance());  // No debe cambiar, el saldo sigue siendo 500

} catch (Exception $e) {
    // En caso de error, se captura y muestra el mensaje
    pl($e->getMessage());
}

// --------- [Prueba 8: Verificar retiro permitido por funcionalidad antifraude] --------
pl('--------- [Prueba 8: Verificar retiro permitido por funcionalidad antifraude] --------');
try {
    // Crear una cuenta bancaria con un saldo inicial de 5000.0
    $bankAccount = new BankAccount(5000.0);  // Balance inicial
    pl('Saldo antes del retiro: ' . $bankAccount->getBalance());
    
    // Simula un retiro de 1000 (permitido según la API, no bloqueado por fraude)
} catch (Exception $e) {
    // En caso de error, se captura y muestra el mensaje
    pl($e->getMessage());
}


// Clase ApiHelper para utilizar la función de verificación de teléfono
class ApiHelper {
    use \ComBank\Bank\Traits\ApiTrait;
}

// --------- [Prueba 10: Verificación de teléfono] --------
pl('--------- [Prueba 10: Verificación de teléfono] --------');
try {
    // Usamos la función de validación de teléfono proporcionada por ApiTrait
    $apiHelper = new ApiHelper();
    $phoneNumber = '34612345678';
    $phoneInfo = $apiHelper->validatePhone($phoneNumber);  // Se valida el número de teléfono
    
    // Mostrar la información del número de teléfono
    pl('Teléfono: ' . $phoneInfo['phone']);
    pl('Válido: ' . ($phoneInfo['valid'] ? 'Sí' : 'No'));
    pl('Formato internacional: ' . $phoneInfo['international_format']);
    pl('Formato local: ' . $phoneInfo['local_format']);
    pl('País: ' . $phoneInfo['country']['name'] . ' (' . $phoneInfo['country']['code'] . ')');
    pl('Prefijo: ' . $phoneInfo['country']['prefix']);
    pl('Ubicación: ' . $phoneInfo['location']);
} catch (Exception $e) {
    // En caso de error en la validación, se captura y muestra el mensaje
    pl('Error: ' . $e->getMessage());
}
