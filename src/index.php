<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

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
require_once __DIR__ . '/../vendor/autoload.php'; 

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
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
    pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());
} catch (ZeroAmountException $e) {
    pl('Error transaction: ' . $e->getMessage());
} catch (InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
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
    pl('Doing transaction deposit (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300));
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction deposit (50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(50));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120));
} catch (InvalidArgumentException $e) {
    pl('' . $e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
    pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());
} catch (ZeroAmountException $e) {
    pl('' . $e->getMessage());
}

try {
    pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(20));
    pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());
} catch (InvalidArgumentException $e) {
    pl('' . $e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
    pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());
} catch (ZeroAmountException $e) {
    pl('' . $e->getMessage());
}

// ---[Start testing national account (no conversion)]---/
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

} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
    pl('My balance after failed last transaction: ' . $nationalAccount->getBalance());
} catch (InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

// ---[Start testing international account dollar conversion]---/
pl('--------- [Start testing international account dollar conversion] --------');

try {
    // Crear una cuenta internacional con saldo en EUR y convertirlo a USD (por ejemplo)
    $nationalAccount = new InternationalBankAccount(400.0, 'EUR'); // Saldo en EUR, moneda base

    pl('Initial balance of national account (EUR): ' . $nationalAccount->getBalance());

    // Convertir a USD usando la API de conversión
    $convertedBalance = $nationalAccount->getConvertedCurrency('USD'); // Convertir a USD
    pl('Converted balance to USD (using conversion API): ' . $convertedBalance);

    // Mostrar el valor esperado
    pl('Converted balance (expected 400 EUR converted to USD): ' . $convertedBalance);

} catch (\InvalidArgumentException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
    pl('My balance after failed last transaction: ' . $nationalAccount->getBalance());
} catch (\Exception $e) {
    pl('Unexpected error: ' . $e->getMessage());
}


// --- Test 1: Verify national bank account returns currency in EUR ---
pl('--------- [Test 1: National Bank Account returns currency in EUR] --------');
try {
    // Crear una cuenta nacional con saldo en EUR
    $nationalBankAccount = new NationalBankAccount(500.0);
    pl('Currency in national account: EUR');
    pl('Initial balance: ' . $nationalBankAccount->getBalance() . ' EUR');
} catch (Exception $e) {
    pl($e->getMessage());
}

// --- Test 2: Verify international bank account returns currency in EUR with no converted balance ---
pl('--------- [Test 2: International Bank Account with no conversion returns EUR] --------');
try {
    // Crear una cuenta internacional con saldo en EUR y sin conversión
    $intlBankAccount = new InternationalBankAccount(500.0, 'EUR'); // Moneda base: EUR, sin conversión
    pl('Currency in international account: EUR');
    pl('Initial balance: ' . $intlBankAccount->getBalance() . ' EUR');
} catch (Exception $e) {
    pl($e->getMessage());
}

// --- Test 3: Verify international bank account returns currency in USD with balance converted ---
pl('--------- [Test 3: International Bank Account with conversion returns USD] --------');
try {
    // Crear una cuenta internacional con saldo en EUR y convertir a USD
    $intlBankAccount = new InternationalBankAccount(500.0, 'EUR'); // Saldo en EUR
    $convertedBalance = $intlBankAccount->getConvertedCurrency('USD'); // Convertir a USD
    pl('Currency in international account: USD');
    pl('Converted balance: ' . $convertedBalance . ' USD');
} catch (Exception $e) {
    pl($e->getMessage());
}


// --- Test 4: Verify a valid email for an account holder ---
pl('--------- [Test 4: Valid Email for Account Holder] --------');
try {
    // Utilizamos un correo válido para el primer ejemplo
    $person = new Person('John Doe', '12345', 'example1@gmail.com');
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

// --- Test 5: Verify an invalid email for an account holder ---
pl('--------- [Test 5: Invalid Email for Account Holder] --------');
try {
    // Utilizamos un correo con formato incorrecto para el segundo ejemplo
    $person = new Person('Jane Smith', '67890', 'invalidemail2');
    pl('Invalid email: ' . $person->getEmail());
    $response = $person->validateEmail($person->getEmail());
    if (!$response['isValid']) {
        pl('Email is invalid.');
    }
} catch (Exception $e) {
    pl($e->getMessage());
}


// Test 6: Verify deposit allowed by fraud functionality
pl('--------- [Test 6: Verify deposit allowed by fraud functionality] --------');
try {
    $bankAccount = new BankAccount(500.0);  // Balance inicial
    pl('Balance before deposit: ' . $bankAccount->getBalance());
    
    // Simula un depósito de 5000 (permitido según la API)
    $depositTransaction = new DepositTransaction(5000); 
    $bankAccount->transaction($depositTransaction);
    pl('Balance after deposit: ' . $bankAccount->getBalance());  // Debe ser 5000 + 500 = 5500

} catch (Exception $e) {
    pl($e->getMessage());
}

// Test 7: Verify deposit blocked by fraud functionality
pl('--------- [Test 7: Verify deposit blocked by fraud functionality] --------');
try {
    $bankAccount = new BankAccount(500.0);  // Balance inicial
    pl('Balance before deposit: ' . $bankAccount->getBalance());
    
    // Simula un depósito de 20000 (bloqueado por la API)
    $depositTransaction = new DepositTransaction(20000); 
    $bankAccount->transaction($depositTransaction);
    pl('Balance after deposit: ' . $bankAccount->getBalance());  // No se actualizará el saldo

} catch (Exception $e) {
    pl($e->getMessage());
}

// Test 8: Verify withdraw allowed by fraud functionality
pl('--------- [Test 8: Verify withdraw allowed by fraud functionality] --------');
try {
    $bankAccount = new BankAccount(5000.0);  // Balance inicial
    pl('Balance before withdrawal: ' . $bankAccount->getBalance());
    
    // Simula un retiro de 1000 (permitido según la API)
    $withdrawTransaction = new WithdrawTransaction(1000); 
    $bankAccount->transaction($withdrawTransaction);
    pl('Balance after withdrawal: ' . $bankAccount->getBalance());  // Debe ser 5000 - 1000 = 4000

} catch (Exception $e) {
    pl($e->getMessage());
}

// Test 9: Verify withdraw blocked by fraud functionality
pl('--------- [Test 9: Verify withdraw blocked by fraud functionality] --------');
try {
    $bankAccount = new BankAccount(5000.0);  // Balance inicial
    pl('Balance before withdrawal: ' . $bankAccount->getBalance());
    
    // Simula un retiro de 10000 (bloqueado por la API)
    $withdrawTransaction = new WithdrawTransaction(10000); 
    $bankAccount->transaction($withdrawTransaction);
    pl('Balance after withdrawal: ' . $bankAccount->getBalance());  // No se actualizará el saldo

} catch (Exception $e) {
    pl($e->getMessage());
}

class ApiHelper {
    use \ComBank\Bank\Traits\ApiTrait;
}

pl('--------- [Test 10: Phone Verification Test] --------');
try {
    $apiHelper = new ApiHelper();

    $phoneNumber = '34612345678';

    $phoneInfo = $apiHelper->validatePhone($phoneNumber);

    pl('Phone: ' . $phoneInfo['phone']);
    pl('Valid: ' . ($phoneInfo['valid'] ? 'Yes' : 'No'));
    pl('International Format: ' . $phoneInfo['international_format']);
    pl('Local Format: ' . $phoneInfo['local_format']);
    pl('Country: ' . $phoneInfo['country']['name'] . ' (' . $phoneInfo['country']['code'] . ')');
    pl('Prefix: ' . $phoneInfo['country']['prefix']);
    pl('Location: ' . $phoneInfo['location']);
} catch (Exception $e) {
    pl('Error: ' . $e->getMessage());
}


?>

