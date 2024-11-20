<?php

namespace ComBank\Bank\Traits;

use ComBank\Transactions\Contracts\BankTransactionInterface;

trait ApiTrait
{
    private string $fraudApiUrl = 'https://673b5147339a4ce4451baa5a.mockapi.io/FraudDetection/transactions';
    private string $emailValidationApiUrl = 'https://emailvalidation.abstractapi.com/v1/';
    private string $emailApiKey = '2d07314255484ac29e131d918e04dcf1';

    private function makeApiRequest(string $url, array $postData = null): array
    {
        $ch = curl_init($url);
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
        ];

        if ($postData !== null) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($postData);
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        if ($error = curl_errno($ch)) {
            throw new \Exception('cURL error: ' . curl_strerror($error));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new \Exception("API error: Invalid response from $url with status code $httpCode");
        }

        curl_close($ch);

        return json_decode($response, true) ?: [];
    }

    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $apiUrl = "https://open.er-api.com/v6/latest/$fromCurrency";
        
        $response = $this->makeApiRequest($apiUrl);
        if (!isset($response['rates'][$toCurrency])) {
            throw new \Exception("No se pudo obtener la tasa de conversión para $toCurrency.");
        }

        $conversionRate = $response['rates'][$toCurrency];
        return $amount * $conversionRate;
    }

    public function validateEmail(string $email): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format.');
        }

        $apiUrl = $this->emailValidationApiUrl . "?api_key={$this->emailApiKey}&email=" . urlencode($email);
        $response = $this->makeApiRequest($apiUrl);

        return [
            'isValid' => $response['is_valid_format']['value'] ?? false,
            'deliverability' => $response['deliverability'] ?? 'unknown',
            'email' => $email,
        ];
    }

    public function detectFraud(BankTransactionInterface $transaction): array
    {
        $postData = [
            'movementType' => $transaction->getTransaction(),
            'amount' => $transaction->getAmount(),
        ];

        return $this->makeApiRequest($this->fraudApiUrl, $postData);
    }
}
