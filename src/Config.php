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

namespace Fan\ElasticBoolQuery;

use JetBrains\PhpStorm\ArrayShape;

class Config
{
    public function __construct(
        protected array $hosts,
        #[ArrayShape([
            'refresh' => 'bool',
            'retry_on_conflict' => 'int',
        ])]
        protected array $updateSettings = [],
    ) {
    }

    public function getHosts(): array
    {
        return $this->hosts;
    }

    public function getUpdateSettings(): array
    {
        return $this->updateSettings;
    }
}
