<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Transaction;
use Tests\TestCase;

class ApiTransactionControllerTest extends TestCase
{
    public function test_send_money_must_be_for_authorized_user()
    {
        $this->json('POST', 'api/send-money')->assertUnauthorized();
    }
    public function test_send_money_create()
    {
        $senderUser = User::factory()->create(['wallet' => 100]);
        $this->actingAs($senderUser, 'api');
        $receiverUser = User::factory()->create();

        $formData = [
            'sender_id' => $senderUser->id,
            'receiver_id' => $receiverUser->id,
            'amount' => $senderUser->wallet - 90,
        ];
        $this->json('POST', 'api/send-money', $formData)
            ->assertStatus(201);
    }

    public function test_send_money_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $this->json('POST', 'api/send-money')
            ->assertStatus(422)
            ->assertJson([
                "success" => false,
                "message" => [
                    "sender_id" => [
                        "The sender id field is required."
                    ],
                    "receiver_id" => [
                        "The receiver id field is required."
                    ],
                    "amount" => [
                        "The amount field is required."
                    ]
                ],
                "data" => [
                    "error" => "Validation errors"
                ]
            ]);


        $this->json('POST', 'api/send-money', ['receiver_id' => 'abc', 'amount' => 'xyz'])
            ->assertStatus(422)
            ->assertJson([
                "success" => false,
                "message" => [
                    "receiver_id" => [
                        "The receiver id must be numeric"
                    ],
                    "amount" => [
                        "The amount format is invalid.",
                        "The amount must be greater than 0 characters."
                    ],
                ],
                "data" => [
                    "error" => "Validation errors"
                ]
            ]);
    }

    public function test_send_money_validate_invalid_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $this->json('POST', 'api/send-money', ['receiver_id' => $user->id + 1, 'amount' => 10])->assertUnprocessable();
    }

    public function test_send_money_not_possible_to_own_wallet()
    {
        $senderUser = User::factory()->create();
        $this->actingAs($senderUser, 'api');

        $this->json('POST', 'api/send-money', ['receiver_id' => $senderUser->id, 'sender_id' => $senderUser->id, 'amount' => 10])->assertUnprocessable();
    }

    public function test_send_money_not_possible_for_insufficient_wallet_balance()
    {
        $senderUser = User::factory()->create(['wallet' => 10]);
        $receiverUser = User::factory()->create();
        $this->actingAs($senderUser, 'api');
        $this->json('POST', 'api/send-money', ['receiver_id' => $receiverUser->id, 'amount' => 11])->assertUnprocessable();
    }

    public function test_user_transaction_info_return_valid_result()
    {
        $user1 = User::factory()->create(['currency_id' => '97']);
        $user2 = User::factory()->create(['currency_id' => '31']);
        $user3 = User::factory()->create(['currency_id' => '97']);

        $this->actingAs($user1, 'api');

        Transaction::factory()->create([
            'sender_id' => $user1->id,
            'sender_currency' => $user1->currency->code,
            'sending_amount' => 10.00,
            'receiver_id' => $user2->id,
            'receiver_currency' => $user2->currency->code,
            'receiving_amount' => 11.00,
        ]);

        Transaction::factory()->create([
            'sender_id' => $user1->id,
            'sender_currency' => $user1->currency->code,
            'sending_amount' => 20.00,
            'receiver_id' => $user2->id,
            'receiver_currency' => $user2->currency->code,
            'receiving_amount' => 22.00,
        ]);

        Transaction::factory()->create([
            'sender_id' => $user2->id,
            'sender_currency' => $user2->currency->code,
            'sending_amount' => 27.00,
            'receiver_id' => $user1->id,
            'receiver_currency' => $user1->currency->code,
            'receiving_amount' => 30.00,
        ]);

        Transaction::factory()->create([
            'sender_id' => $user2->id,
            'sender_currency' => $user2->currency->code,
            'sending_amount' => 36.00,
            'receiver_id' => $user1->id,
            'receiver_currency' => $user1->currency->code,
            'receiving_amount' => 40.00,
        ]);

        Transaction::factory()->create([
            'sender_id' => $user1->id,
            'sender_currency' => $user1->currency->code,
            'sending_amount' => 50.00,
            'receiver_id' => $user3->id,
            'receiver_currency' => $user3->currency->code,
            'receiving_amount' => 50.00,
        ]);

        $sendingConvertedAmount = 30;
        $receivingConvertedAmount = 70;
        $thirdHighestTransaction = 30;

        $this->json('GET', 'api/user-transaction-info')
            ->assertOk()
            ->assertJsonPath('data.converted_amount_by_sending', number_format($sendingConvertedAmount, 2))
            ->assertJsonPath('data.converted_amount_by_receiving', number_format($receivingConvertedAmount, 2))
            ->assertJsonPath('data.total_converted_amount', number_format(($sendingConvertedAmount + $receivingConvertedAmount), 2))
            ->assertJsonPath('data.third_highest_amount', number_format($thirdHighestTransaction, 2));
    }
}
