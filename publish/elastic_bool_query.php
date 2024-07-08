<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'hosts' => [
        '127.0.0.1:9200',
    ],
    // 'read' => [
    //     'hosts' => ['127.0.0.1:9200'],
    // ],
    // 'write' => [
    //     'hosts' => ['127.0.0.1:9200'],
    // ],
    'update_settings' => [
        'refresh' => true,
        'retry_on_conflict' => 5,
    ],
    'indices_settings' => [
        'number_of_shards' => 4,
    ],
];
