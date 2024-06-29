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

class CustomDocument extends Document
{
    public function __construct(protected string $index)
    {
    }

    public function getIndex(): string
    {
        return $this->index;
    }
}
