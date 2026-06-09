<?php

namespace App\Traits;

trait CalculatesCurrency
{
    /**
     * Compute locked USD & IQD amounts from an entered amount + currency + rate.
     */
    protected function currencyAmounts(string $currency, float $amount, float $rate): array
    {
        if ($currency === 'USD') {
            return [
                'amount_usd' => round($amount, 4),
                'amount_iqd' => round($amount * $rate, 2),
            ];
        }

        return [
            'amount_iqd' => round($amount, 2),
            'amount_usd' => round($amount / $rate, 4),
        ];
    }
}
