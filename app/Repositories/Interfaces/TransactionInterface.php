<?php

namespace App\Repositories\Interfaces;

use App\Models\Transaction;

interface TransactionInterface
{
    public function createTransaction(array $transactionDetails): Transaction;
    public function getMostConversion(): ?Transaction;
    public function getThirdHighestTransactionByUserId(int $userId): array;
    public function getTotalSendingAmountByUserId(int $userId): float;
    public function getTotalReceivingAmountByUserId(int $userId): float;
}
