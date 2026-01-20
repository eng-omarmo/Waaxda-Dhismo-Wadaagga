<?php

return [
    'endpoints' => [
        'verify' => 'http://localhost:8080/somxchange-main/api/v2/merchant/api/verify',
        'transaction' => 'http://localhost:8080/somxchange-main/api/v2/merchant/api/transaction-info',
        'transaction-verify' => 'http://localhost:8080/somxchange-main/api/v2/merchant/api/verify-transaction',
    ],
    'credentials' => [
        'client_id' => env('SOMX_CLIENT_ID'),
        'client_secret' => env('SOMX_CLIENT_SECRET'),
    ],
];
