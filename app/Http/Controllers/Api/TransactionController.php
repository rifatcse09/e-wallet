<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Requests\SendMoneyRequest;
use App\Repositories\Interfaces\CurrencyConversionInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\TransactionInterface;
use App\Traits\RespondsWithHttpStatusTrait;
use App\Events\TransactionProcessed;

use Log;

class TransactionController extends Controller
{
    use RespondsWithHttpStatusTrait;
    private $_TransactionRepos;
    private $_ConversionRepos;
    private $_UserRepos;

    public function __construct(
        TransactionInterface $transactionRepository,
        CurrencyConversionInterface $currencyConversionRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->_TransactionRepos = $transactionRepository;
        $this->_ConversionRepos = $currencyConversionRepository;
        $this->_UserRepos = $userRepository;
    }

    public function currencyApiCheck(): JsonResponse
    {
        $currencyConversion = $this->_ConversionRepos->getCurrencyConversionData('USD', 'EUR', 100);

        if (!empty($currencyConversion)) {
            if ($currencyConversion['status']) {
                $receivingAmount  = $currencyConversion['amount'];
                return $this::success($receivingAmount, __('messages.success_message'));
            } else {
                return $this::failure(__('messages.currency_convert_api_error'), $currencyConversion['error_message'], $currencyConversion['error_code']);
            }
        }
    }


    public function sendMoney(SendMoneyRequest $request): JsonResponse
    {
        try {

            $senderReciverInfo = $this->_UserRepos->getSenderAndReceiverInfo($request->all());

            if ($request->amount > $senderReciverInfo['sender']['wallet'])

                return $this::failure(__('messages.wallet_insufficient'), ['error' => 'Validation errors']);

            /* Currency Conversion start */
            $senderReciverInfo['receiver']['receiving_amount'] = $request->amount;
            $senderReciverInfo['sender']['sending_amount'] = $request->amount;

            if ($senderReciverInfo['sender']['currency'] !== $senderReciverInfo['receiver']['currency']) {
                $convertReponse = $this->_ConversionRepos->getCurrencyConversionData($senderReciverInfo['sender']['currency'], $senderReciverInfo['receiver']['currency'], $request->amount);

                if (!$convertReponse['status']) {
                    return $this::failure(__('messages.currency_convert_api_error'), $convertReponse['error_message'], $convertReponse['error_code']);
                }

                $senderReciverInfo['receiver']['receiving_amount'] = $convertReponse['amount'];
            }
            /* Currency Conversion end */

            /* Transaction start */
            try {

                DB::beginTransaction();

                $senderWallet = $senderReciverInfo['sender']['wallet'] - $senderReciverInfo['sender']['sending_amount'];
                $receiverWallet = $senderReciverInfo['receiver']['wallet']  + $senderReciverInfo['receiver']['receiving_amount'];

                $this->_UserRepos->updateUser($senderReciverInfo['sender']['id'], ['wallet' => $senderWallet]);
                $this->_UserRepos->updateUser($senderReciverInfo['receiver']['id'], ['wallet' => $receiverWallet]);

                $transaction = $this->_TransactionRepos->createTransaction($senderReciverInfo);

                DB::commit();

                /* Mail start */
                TransactionProcessed::dispatch($transaction);

                return $this::success($transaction, __('messages.success_message'), Response::HTTP_CREATED);
            } catch (\Exception $e) {
                DB::rollback();
                return $this::failure($e->getMessage(), __('messages.transaction_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            /* Transaction end */
        } catch (\Exception $e) {
            return
                $this::failure($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function userTransactionInfo(): JsonResponse
    {
        $user = $this->_UserRepos->getAuthUser();

        $sendingConvertedAmount = $this->_TransactionRepos->getTotalSendingAmountByUserId($user->id);
        $receivingConvertedAmount = $this->_TransactionRepos->getTotalReceivingAmountByUserId($user->id);
        $mostConversion = $this->_TransactionRepos->getMostConversion();
        $thirdHighestTransaction = $this->_TransactionRepos->getThirdHighestTransactionByUserId($user->id);
        $user->converted_amount_by_sending = number_format($sendingConvertedAmount, 2);
        $user->converted_amount_by_receiving = number_format($receivingConvertedAmount, 2);
        $user->total_converted_amount = number_format(($sendingConvertedAmount + $receivingConvertedAmount), 2);
        $user->most_conversion = $mostConversion;
        $user->third_highest_amount = !empty($thirdHighestTransaction) ? number_format(($thirdHighestTransaction[0]->transactionamount), 2) : "0.00";

        return $this::success($user, __('messages.success_message'));
    }
}
