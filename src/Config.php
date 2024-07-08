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
        protected array $writeHosts,
        protected array $readHosts = [],
        #[ArrayShape([
            'refresh' => 'bool',
            'retry_on_conflict' => 'int',
        ])]
        protected array $updateSettings = [],
        #[ArrayShape([
            'number_of_shards' => 'int',
        ])]
        protected array $indicesSettings = [],
    ) {
        if (! $this->readHosts) {
            $this->readHosts = $this->writeHosts;
        }
    }

    public function getReadHosts(): array
    {
        return $this->readHosts;
    }

    public function getWriteHosts(): array
    {
        return $this->writeHosts;
    }

    public function getUpdateSettings(): array
    {
        return $this->updateSettings;
    }

    public function getIndicesSettings(): array
    {
        return $this->indicesSettings;
    }
}
