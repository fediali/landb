<?php

return [
    'locations' => [
        'header-menu' => 'Header Navigation',
        'main-menu'   => 'Main Navigation',
        'categories-menu'   => 'Categories Navigation',
        'footer-menu' => 'Footer Navigation',
        'social-media-menu' => 'Social Media Menu',
    ],
    'cache'     => [
        'enabled' => env('CACHE_FRONTEND_MENU', false),
    ],
];