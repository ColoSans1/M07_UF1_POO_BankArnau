<?php

namespace ComBank\Bank\trait;

trait ApiTrait
{
    private $apiUrl = 'https://open.er-api.com/v6/latest/EUR';
    private $fraudAPI = 'https://673b5147339a4ce4451baa5a.mockapi.io/FraudDetection/transactions';

    private $apiUrlGmail = 'https://emailvalidation.abstractapi.com/v1/?api_key=2d07314255484ac29e131d918e04dcf1&email=';


    private function makeApiRequest(string $url): string    
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10
        ]);
        
        $data = curl_exec($ch);
        if ($error = curl_errno($ch)) {
            throw new \Exception('cURL error: ' . curl_strerror($error));
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            throw new \Exception('API error: Invalid response');
        }
        curl_close($ch);
        
        return $data;
    }

    public function convertCurrency(float $amount, string $from = 'EUR', string $to = 'USD'): float
    {
        $response = $this->makeApiRequest($this->apiUrl);
        $rates = json_decode($response, true)['rates'] ?? [];
        
        if (!isset($rates[$to])) {
            throw new \InvalidArgumentException("Unsupported target currency: {$to}");
        }
        
        return ($amount / $rates[$from]) * $rates[$to];
    }

    public function verificationGmail(string $email): array
    {
        if (empty($email)) {
            throw new \Exception('Email is required.');
        }
    
        $encodedEmail = urlencode($email);
        $response = $this->makeApiRequest($this->apiUrlGmail . $encodedEmail);
        $data = json_decode($response, true);
    
        if (isset($data['error'])) {
            throw new \Exception('API error: ' . $data['error']['message']);
        }
    
        if (!isset($data['is_valid_format']['value']) || !$data['is_valid_format']['value']) {
            throw new \Exception('Invalid email format.');
        }
    
        if (isset($data['deliverability']) && strtolower($data['deliverability']) === 'undeliverable') {
            return ['status' => 'undeliverable', 'email' => $email];
        }
    
        return ['status' => 'valid', 'email' => $email];
    }

    
}
