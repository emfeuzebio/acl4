<?php

use Illuminate\Support\Facades\Route;
use App\EmailService\Http\Controllers\EmailController;

Route::post('/v1/email/send', [EmailController::class, 'send'])
    ->middleware('email.api.key')
    ->name('email-service.send');