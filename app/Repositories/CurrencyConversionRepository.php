<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CurrencyConversionInterface;
use Illuminate\Support\Facades\Http;


class CurrencyConversionRepository  implements CurrencyConversionInterface
{
    private string $apikey;

    public function __construct()
    {
        $this->apikey = config('api.key');
    }

    public function getCurrencyConversionData(string $fromCurrency, string $toCurrency, float $amount): array
    {

        $currencyConversion =  Http::accept('application/json')
            ->withHeaders(['apikey' => $this->apikey])
            ->get('https://api.apilayer.com/fixer/convert', [
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'amount' => $amount,
            ]);

        return $this->currencyConvertResult($currencyConversion);
    }

    private function currencyConvertResult($conversion): array
    {

        $result = [];

        if ($conversion->successful()) {

            $currencyConversionObj = $conversion->object();
            $result = $currencyConversionObj->success ? $this->successResult($currencyConversionObj) : $this->configErrorCheck($currencyConversionObj);
        } else {

            $result = $this->serverErrorCheck($conversion);
        }
        return !empty($result) ? $result : [
            'status' => false,
            'error_message' => 'error not detected',
            'error_code' => 404,
        ];
    }

    private function successResult($currencyConversionObj): array
    {

        return [
            'status' => true,
            'amount' => $currencyConversionObj->result
        ];
    }

    private function configErrorCheck($currencyConversionObj): array
    {

        return [
            'status' => false,
            'error_message' => $currencyConversionObj->error->info,
            'error_code' => $currencyConversionObj->error->code
        ];
    }

    private function serverErrorCheck($conversion): array
    {

        return [
            'status' => false,
            'error_message' => $conversion->clientError() ? $conversion->object()->message : __('messages.currency_convert_server_error'),
            'error_code' => $conversion->status()
        ];
    }
}
