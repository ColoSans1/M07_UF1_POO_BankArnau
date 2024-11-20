<?php

namespace ComBank\Bank\Traits;

use ComBank\Transactions\Contracts\BankTransactionInterface;

trait ApiTrait
{
    private string $fraudApiUrl = 'https://673b5147339a4ce4451baa5a.mockapi.io/FraudDetection/transactions';
    private string $apiUrlGmail = 'https://emailvalidation.abstractapi.com/v1/?api_key=5ea504eea90045e989ed2390a189de40&email=';
    private string $apiUrlPhone = 'https://phonevalidation.abstractapi.com/v1/?api_key=4e50b2d406e44fc082cf28ba7f4c5aad&phone=';

    /**
     * Realiza una solicitud API genérica
     */
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
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("API error: Invalid response from $url with status code $httpCode");
        }

        return json_decode($response, true) ?? [];
    }

    /**
     * Validar número de teléfono
     */
    public function validatePhone(string $phone): array
    {
        if (empty($phone)) {
            throw new \InvalidArgumentException('Phone number is required.');
        }

        $url = $this->apiUrlPhone . urlencode($phone);
        $response = $this->makeApiRequest($url);

        if (isset($response['error'])) {
            throw new \Exception('API error: ' . $response['error']['message']);
        }

        return [
            'phone' => $response['phone'] ?? $phone,
            'valid' => $response['valid'] ?? false,
            'international_format' => $response['format']['international'] ?? '',
            'local_format' => $response['format']['local'] ?? '',
            'country' => [
                'code' => $response['country']['code'] ?? '',
                'name' => $response['country']['name'] ?? '',
                'prefix' => $response['country']['prefix'] ?? '',
            ],
            'location' => $response['location'] ?? 'Unknown',
            'type' => $response['type'] ?? 'Unknown',
            'carrier' => $response['carrier'] ?? 'Unknown',
        ];
    }

    /**
     * Validar dirección de correo electrónico
     */
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

        if (isset($response['error'])) {
            throw new \Exception('API error: ' . $response['error']['message']);
        }

        return [
            'isValid' => $response['is_valid_format']['value'] ?? false,
            'deliverability' => $response['deliverability'] ?? 'unknown',
            'email' => $email,
        ];
    }

    /**
     * Detectar fraude en transacciones
     */
    public function detectFraud(BankTransactionInterface $transaction): array
    {
        $postData = [
            'movementType' => $transaction->getTransaction(),
            'amount' => $transaction->getAmount(),
        ];

        return $this->makeApiRequest($this->fraudApiUrl, $postData);
    }

    /**
     * Convertir monedas usando una API externa
     */
    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $apiUrl = "https://open.er-api.com/v6/latest/$fromCurrency";
        $response = $this->makeApiRequest($apiUrl);

        if (!isset($response['rates'][$toCurrency])) {
            throw new \Exception("No se pudo obtener la tasa de conversión para $toCurrency.");
        }

        return $amount * $response['rates'][$toCurrency];
    }
}
