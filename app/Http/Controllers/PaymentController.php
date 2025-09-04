<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Transaction;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\payments\v2\models\request\StandardCheckoutPayRequest;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutConstants;
use PhonePe\common\exceptions\PhonePeException;

class PaymentController extends Controller
{
    /**
     * Initiate a payment with PhonePe.
     */
    public function initiatePayment(Request $request, StandardCheckoutClient $client)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $amountInPaisa = $request->amount * 100;

        $merchantOrderId = 'MUID' . Str::random(10) . time();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'merchant_order_id' => $merchantOrderId,
            'amount' => $amountInPaisa,
            'status' => 'PENDING',
        ]);

        // The redirect URL is where the user will be sent after completing the payment on the PhonePe page.
        $redirectUrl = route('payment.status', ['merchantOrderId' => $merchantOrderId]);

        // The callback URL is where PhonePe's server will send a server-to-server update.
        $callbackUrl = route('payment.callback');

        try {
            $message = 'Payment for resume plan.';

            $paymentFlow = [
                "type" => StandardCheckoutConstants::STANDARD_CHECKOUT_PAYMENT_FLOW_TYPE,
                "message" => $message,
                "merchantUrls" => [
                    "redirectUrl" => $redirectUrl,
                    "callbackUrl" => $callbackUrl
                ]
            ];

            $payRequest = new StandardCheckoutPayRequest(
                $merchantOrderId,
                $amountInPaisa,
                null,
                $paymentFlow
            );

            $payResponse = $client->pay($payRequest);

            Log::info('PhonePe API Response: ', (array) $payResponse);

            if ($payResponse && $payResponse->getRedirectUrl()) {
                return redirect()->away($payResponse->getRedirectUrl());
            } else {
                Log::error('PhonePe Payment Initiation: No redirect URL received.', (array) $payResponse);
                $transaction->status = 'FAILED';
                $transaction->save();
                return redirect()->route('resume-build')->with('error', 'Could not get payment URL from PhonePe. Please try again.');
            }

        } catch (PhonePeException $e) {
            $transaction->status = 'FAILED';
            $transaction->save();
            Log::error('PhonePe Payment Initiation Failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('resume-build')->with('error', 'Payment initiation failed. Please try again.');
        } catch (\Exception $e) {
            $transaction->status = 'FAILED';
            $transaction->save();
            Log::error('An unexpected error occurred during payment initiation: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('resume-build')->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Handle the server-to-server callback from PhonePe.
     */
    public function handleCallback(Request $request)
    {
        $payload = $request->input('response');
        Log::info('PhonePe Callback Raw Payload: ' . $payload);
        $decodedPayload = json_decode(base64_decode($payload), true);
        Log::info('PhonePe Callback Decoded Payload: ', (array) $decodedPayload);

        if (!$decodedPayload) {
            Log::error('PhonePe Callback: Invalid payload received.');
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        // TODO: Implement webhook verification when credentials are available.
        // $config = config('services.phonepe');
        // $saltKey = $config['salt_key']; // Assuming a salt key is provided for verification
        // $saltIndex = $config['salt_index'];
        // $header = $request->header('X-VERIFY');
        // $calculatedSignature = hash('sha256', $payload . '/pg/v1/pay' . $saltKey) . '###' . $saltIndex;
        // if (!hash_equals($header, $calculatedSignature)) {
        //     Log::error('PhonePe Callback: Signature verification failed.');
        //     return response()->json(['status' => 'error', 'message' => 'Signature verification failed'], 400);
        // }

        $merchantOrderId = $decodedPayload['data']['merchantOrderId'] ?? null;
        if (!$merchantOrderId) {
            Log::error('PhonePe Callback: merchantOrderId not found in payload.');
            return response()->json(['status' => 'error', 'message' => 'merchantOrderId not found'], 400);
        }

        $transaction = Transaction::where('merchant_order_id', $merchantOrderId)->first();

        if ($transaction) {
            if (isset($decodedPayload['data']['transactionId'])) {
                $transaction->phonepe_transaction_id = $decodedPayload['data']['transactionId'];
            }

            $state = $decodedPayload['data']['state'] ?? null;
            if ($state === 'COMPLETED') {
                $transaction->status = 'COMPLETED';
            } else if ($state === 'FAILED') {
                $transaction->status = 'FAILED';
            }
            // For other states like PENDING, we can let the status remain as it is.

            $transaction->save();
            Log::info('PhonePe Callback: Transaction status updated to ' . $transaction->status . ' for ' . $merchantOrderId);
            return response()->json(['status' => 'success']);
        } else {
            Log::warning('PhonePe Callback: Transaction not found for merchantOrderId: ' . $merchantOrderId);
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }
    }

    /**
     * Handle the user redirection after payment attempt.
     */
    public function paymentStatus(Request $request, $merchantOrderId)
    {
        $transaction = Transaction::where('merchant_order_id', $merchantOrderId)
                                  ->where('user_id', Auth::id())
                                  ->firstOrFail();

        // Optional: Call the getOrderStatus() API here to get the latest status from PhonePe
        // and update the transaction record before showing the status to the user.

        if ($transaction->status === 'COMPLETED') {
            return redirect()->route('payment.success');
        } else {
            return redirect()->route('payment.failure');
        }
    }
}
