<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionInterface;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionInterface

{

    public function createTransaction(array $transactionDetails): Transaction
    {

        return Transaction::create([
            'sender_id' => $transactionDetails['sender']['id'],
            'sender_currency' => $transactionDetails['sender']['currency'],
            'sending_amount' => $transactionDetails['sender']['sending_amount'],
            'receiver_id' => $transactionDetails['receiver']['id'],
            'receiver_currency' => $transactionDetails['receiver']['currency'],
            'receiving_amount' => $transactionDetails['receiver']['receiving_amount'],
        ]);
    }

    public function getTotalSendingAmountByUserId(int $userId): float
    {
        return Transaction::whereRaw('sender_currency != receiver_currency')
            ->where('sender_id', $userId)
            ->sum('sending_amount');
    }

    public function getTotalReceivingAmountByUserId(int $userId): float
    {
        return Transaction::whereRaw('sender_currency != receiver_currency')
            ->where('receiver_id', $userId)
            ->sum('receiving_amount');
    }

    public function getMostConversion(): ?Transaction
    {
        return Transaction::select('sender_id', DB::raw('count(id) as conversionQuantity'))
            ->whereRaw('sender_currency != receiver_currency')
            ->groupBy('sender_id')
            ->orderBy('conversionQuantity', 'desc')
            ->first();
    }

    public function getThirdHighestTransactionByUserId(int $userId): array
    {
        $sendingAmount = DB::table("transactions")
            ->select(
                "receiving_amount as transactionamount"
            )->where("receiver_id", "=", $userId);

        return DB::table("transactions")
            ->select(
                "sending_amount as transactionamount"
            )->where("sender_id", "=", $userId)
            ->unionAll($sendingAmount)
            ->offset(2)->limit(1)
            ->orderBy("transactionamount", "desc")
            ->get()->toArray();
    }
}
