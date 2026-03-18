<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;


Route::post('webhook', [App\Http\Controllers\RazorpayWebhookController::class, 'handle']);

