<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class RazorpayWebhookController extends Controller
{


    // public function handle(Request $request)
    // {
    //     $secret = env('RAZORPAY_WEBHOOK_SECRET');
    //     $payload = $request->getContent();
    //     $signature = $request->header('X-Razorpay-Signature');

    //     // Verify Signature
    //     $expectedSignature = hash_hmac('sha256', $payload, $secret);

    //     if (!hash_equals($expectedSignature, $signature)) {
    //         Log::warning('Razorpay webhook signature mismatch.');
    //         return response('Signature verification failed', 400);
    //     }

    //     $event = $request->input('event');

    //     // Process specific event
    //     if ($event === 'payment.captured') {
    //         $paymentData = $request->input('payload.payment.entity');
    //         Log::info('Payment Captured:', $paymentData);
    //         // Your custom logic here
    //     }

    //     return response('Webhook handled', 200);
    // }

    public function handle(Request $request)
{
    Log::info('Webhook received:', $request->all());

    return response()->json(['status' => 'success']);
}
}
