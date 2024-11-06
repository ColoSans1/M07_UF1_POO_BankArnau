<?php

namespace ComBank\A;

trait ApiTrait
{
    private $apiUrl = 'https://open.er-api.com/v6/latest/USD';

    public function convertCurrency(float $amount, string $from = 'USD', string $to = 'EUR'): float
    {
        if ($from !== 'USD') {
            throw new \InvalidArgumentException("This API only supports conversion from USD to another currency.");
        }

        $response = file_get_contents($this->apiUrl);
        $data = json_decode($response, true);

        if (!isset($data['rates'][$to])) {
            throw new \InvalidArgumentException("Unsupported target currency: {$to}");
        }

        $rate = $data['rates'][$to];

        return $amount * $rate;
    }
}
