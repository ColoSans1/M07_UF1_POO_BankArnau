<?php

namespace ComBank\Bank;

trait ApiTrait
{
    private $apiUrl = 'https://open.er-api.com/v6/latest/EUR';

    private $apiUrlGmail = 'https://emailvalidation.abstractapi.com/v1/?api_key=2d07314255484ac29e131d918e04dcf1&email=';

    public function convertCurrency(float $amount, string $from = 'EUR', string $to = 'USD'): float
    {
        if ($from !== 'EUR') {
            throw new \InvalidArgumentException("This API only supports conversion from EUR to another currency.");
        }

        $response = file_get_contents($this->apiUrl);
        $data = json_decode($response, true);

        if (!isset($data['rates'][$to])) {
            throw new \InvalidArgumentException("Unsupported target currency: {$to}");
        }

        $rate = $data['rates'][$to];

        return $amount * $rate;
    }

    public function verificationGmail(string $email): array
    {
        $url = $this->apiUrlGmail . urlencode($email);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
    
        $data = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new \Exception('Error en la solicitud cURL: ' . curl_error($ch));
        }
    
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new \Exception('Error: API no disponible, código de respuesta: ' . $httpCode);
        }
    
        curl_close($ch);
    
        $response = json_decode($data, true);
        if (!isset($response['is_valid'])) {
            throw new \Exception('No se pudo verificar la validez del correo electrónico.');
        }
    
        return $response;
    }
    
}
