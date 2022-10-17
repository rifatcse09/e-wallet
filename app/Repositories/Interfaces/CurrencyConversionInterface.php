<?php

namespace App\Repositories\Interfaces;

interface CurrencyConversionInterface
{
    public function getCurrencyConversionData(string $fromCurrency, string $toCurrency, float $amount): array;
}
