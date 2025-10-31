<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::INFO,
        ],
        'db' => [
            'dsn' => 'mysql:host=127.0.0.1;dbname=orders_db;charset=utf8mb4',
            'user' => 'root',
            'pass' => '',
            'options' => []
        ],
        'kitchen_capacity' => 5,
        'auto_complete_seconds' => 300
    ]
];