<?php

use App\Controllers\OfferController;
use App\Controllers\RecipientController;
use App\Controllers\VoucherController;

$app->get('/api/v1/offers', OfferController::class . ':index');
$app->post('/api/v1/offers', OfferController::class . ':store');

$app->get('/api/v1/recipients', RecipientController::class . ':index');
$app->post('/api/v1/recipients', RecipientController::class . ':store');

$app->post('/api/v1/vouchers/generate', VoucherController::class . ':generateVouchers');
$app->post('/api/v1/vouchers/validate', VoucherController::class . ':validateVoucher');
$app->get('/api/v1/vouchers/recipient', VoucherController::class . ':getRecipientVouchers');


