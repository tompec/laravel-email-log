<?php

use Tompec\EmailLog\MailGunController;

Route::post('/email-log/mailgun', [MailGunController::class, 'handleWebhook'])->name('email-log-mailgun-webhook');
