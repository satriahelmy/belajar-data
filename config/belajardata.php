<?php

return [
    'content_path' => env('BELAJARDATA_CONTENT_PATH', base_path('content')),
    'datasets_path' => env('BELAJARDATA_DATASETS_PATH', base_path('datasets')),
    'content_cache_ttl_days' => (int) env('BELAJARDATA_CONTENT_CACHE_TTL_DAYS', 1),
        'planned_components' => [],
];
