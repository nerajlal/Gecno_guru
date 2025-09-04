<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\payments\v2\models\response\StandardCheckoutPayResponse;
use Mockery;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can initiate a payment.
     *
     * @return void
     */
    public function test_a_user_can_initiate_a_payment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $mockPayResponse = Mockery::mock(StandardCheckoutPayResponse::class);
        $mockPayResponse->shouldReceive('getRedirectUrl')->andReturn('https://phonepe.com/pay');

        $mockClient = Mockery::mock(StandardCheckoutClient::class);
        $mockClient->shouldReceive('pay')->andReturn($mockPayResponse);

        $this->app->instance(StandardCheckoutClient::class, $mockClient);

        $response = $this->post(route('payment.initiate'), ['amount' => 299]);

        $response->assertStatus(302);
        $response->assertRedirect('https://phonepe.com/pay');

        $this->assertDatabaseHas('resume_transactions', [
            'user_id' => $user->id,
            'amount' => 29900,
            'status' => 'PENDING',
        ]);
    }

    /**
     * Test that the application can handle a PhonePe callback.
     *
     * @return void
     */
    public function test_it_handles_a_successful_phonepe_callback()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'status' => 'PENDING',
        ]);

        $callbackPayload = [
            'data' => [
                'merchantOrderId' => $transaction->merchant_order_id,
                'transactionId' => 'T123456789',
                'state' => 'COMPLETED',
            ],
        ];

        $encodedPayload = base64_encode(json_encode($callbackPayload));

        $response = $this->postJson(route('payment.callback'), ['response' => $encodedPayload]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('resume_transactions', [
            'merchant_order_id' => $transaction->merchant_order_id,
            'status' => 'COMPLETED',
            'phonepe_transaction_id' => 'T123456789',
        ]);
    }

    /**
     * Test that the application can handle a failed PhonePe callback.
     *
     * @return void
     */
    public function test_it_handles_a_failed_phonepe_callback()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'status' => 'PENDING',
        ]);

        $callbackPayload = [
            'data' => [
                'merchantOrderId' => $transaction->merchant_order_id,
                'transactionId' => 'T123456789',
                'state' => 'FAILED',
            ],
        ];

        $encodedPayload = base64_encode(json_encode($callbackPayload));

        $response = $this->postJson(route('payment.callback'), ['response' => $encodedPayload]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('resume_transactions', [
            'merchant_order_id' => $transaction->merchant_order_id,
            'status' => 'FAILED',
            'phonepe_transaction_id' => 'T123456789',
        ]);
    }
}
