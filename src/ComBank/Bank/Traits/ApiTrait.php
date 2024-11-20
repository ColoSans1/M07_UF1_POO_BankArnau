<?php

namespace ComBank\Bank\Traits;

use ComBank\Transactions\Contracts\BankTransactionInterface;

trait ApiTrait
{
    private string $fraudApiUrl = 'https://673b5147339a4ce4451baa5a.mockapi.io/FraudDetection/transactions';
    private string $apiUrlGmail = 'https://emailvalidation.abstractapi.com/v1/?api_key=5ea504eea90045e989ed2390a189de40&email=';
    private string $apiUrlPhone = 'https://phonevalidation.abstractapi.com/v1/?api_key=4e50b2d406e44fc082cf28ba7f4c5aad&phone=';


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
    

    public function validatePhone(string $phone): array
    {
        if (empty($phone)) {
            throw new \InvalidArgumentException('Phone number is required.');
        }

        $url = $this->apiUrlPhone . urlencode($phone);
        $response = $this->makeApiRequest($url);
        $data = json_decode($response, true);

        if (isset($data['error'])) {
            throw new \Exception('API error: ' . $data['error']['message']);
        }

        return [
            'phone' => $data['phone'] ?? $phone,
            'valid' => $data['valid'] ?? false,
            'international_format' => $data['format']['international'] ?? '',
            'local_format' => $data['format']['local'] ?? '',
            'country' => [
                'code' => $data['country']['code'] ?? '',
                'name' => $data['country']['name'] ?? '',
                'prefix' => $data['country']['prefix'] ?? '',
            ],
            'location' => $data['location'] ?? 'Unknown',
            'type' => $data['type'] ?? 'Unknown',
            'carrier' => $data['carrier'] ?? 'Unknown',
        ];
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
