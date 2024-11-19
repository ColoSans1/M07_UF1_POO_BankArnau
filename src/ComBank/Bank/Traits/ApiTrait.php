<?php

namespace ComBank\Bank\Traits;

use ComBank\Transactions\Contracts\BankTransactionInterface;

trait ApiTrait
{
    private string $fraudApiUrl = 'https://673b5147339a4ce4451baa5a.mockapi.io/FraudDetection/transactions';
    private string $apiUrlGmail = 'https://emailvalidation.abstractapi.com/v1/?api_key=2d07314255484ac29e131d918e04dcf1&email=';

    private function makeApiRequest(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $data = curl_exec($ch);
        if ($error = curl_errno($ch)) {
            throw new \Exception('cURL error: ' . curl_strerror($error));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            $errorMessage = "API error: Invalid response from $url with status code $httpCode";
            throw new \Exception($errorMessage);
        }

        curl_close($ch);
        return $data;
    }

    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $apiUrl = "https://open.er-api.com/v6/latest/" . $fromCurrency; 
        try {
            $response = file_get_contents($apiUrl);
            if ($response === false) {
                throw new \Exception("Error al obtener datos de la API de conversión.");
            }
            $data = json_decode($response, true);
            
            if (!isset($data['rates'][$toCurrency])) {
                throw new \Exception("No se pudo obtener la tasa de conversión para la moneda destino.");
            }
            
            $conversionRate = $data['rates'][$toCurrency];
            return $amount * $conversionRate;
        } catch (\Exception $e) {
            throw new \Exception("Error durante la conversión: " . $e->getMessage());
        }
    }
    
    

    public function validateEmail(string $email): array
    {
        if (empty($email)) {
            throw new \InvalidArgumentException('Email is required.');
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format.');
        }
    
        $url = $this->apiUrlGmail . urlencode($email);
        $response = $this->makeApiRequest($url);
        $data = json_decode($response, true);
    
        if (isset($data['error'])) {
            throw new \Exception('API error: ' . $data['error']['message']);
        }
    
        $isValid = $data['is_valid_format']['value'] ?? false;
        $deliverability = $data['deliverability'] ?? 'unknown';
    
        return [
            'isValid' => $isValid,
            'deliverability' => $deliverability,
            'email' => $email,
        ];
    }
    
    public function detectFraud(BankTransactionInterface $transaction): array
    {  
        $data = [
            'movementType' => $transaction->getTransaction(),
            'amount' => $transaction->getAmount(),
        ];

        $ch = curl_init($this->fraudApiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
        ]);

        $response = curl_exec($ch);

        if ($error = curl_errno($ch)) {
            throw new \Exception('cURL error: ' . curl_strerror($error));
        }

        curl_close($ch);

        return json_decode($response, true) ?: [];
    }
}
