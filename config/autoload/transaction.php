<?php

declare(strict_types=1);

/**
 * Configurações do módulo de transações.
 */
return [
    'cache' => [
        'ttl' => 86400, // 24 horas
        'prefix' => 'transaction:idempotency:',
    ],

    'notification' => [
        'priority' => [
            'high_threshold' => 10000,  // R$ 100.00 em centavos
            'medium_threshold' => 1000, // R$ 10.00 em centavos
            'high_priority' => 10,
            'medium_priority' => 7,
            'normal_priority' => 5,
        ],
    ],

    'rabbitmq' => [
        'max_retries' => 5,
        'retry_delays' => [1, 5, 10, 30, 60], // segundos
        'message_ttl' => 86400000, // 24 horas em milissegundos
        'consumer_workers' => 2, // número de workers paralelos
    ],

    'authorization' => [
        'timeout' => 5, // segundos
        'retry_attempts' => 3,
        'retry_base_delay' => 100, // milissegundos
    ],
];
