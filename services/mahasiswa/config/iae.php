<?php

return [
    'services' => [
        'krs' => [
            'url' => rtrim(env('KRS_SERVICE_URL', 'http://krs-service:8000'), '/'),
            'key' => env('KRS_SERVICE_KEY', 'KEY-MHS-109'),
        ],
        'kurikulum_nilai' => [
            'url' => rtrim(env('KURIKULUM_NILAI_SERVICE_URL', 'http://kurikulum-nilai-service:8000'), '/'),
            'key' => env('KURIKULUM_NILAI_SERVICE_KEY', 'KEY-MHS-117'),
        ],
    ],
];
