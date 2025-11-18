<?php

return [
    'api_key' => env('BEEM_API_KEY'),
    'secret_key' => env('BEEM_SECRET_KEY'),
    'sender_id' => env('BEEM_SENDER_ID', 'Adilisha Portal'),
    'base_url' => env('BEEM_BASE_URL', 'https://apisms.beem.africa/v1'),
];
