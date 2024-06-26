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

class Config
{
    public function __construct(protected array $hosts)
    {
    }

    public function getHosts(): array
    {
        return $this->hosts;
    }
}
