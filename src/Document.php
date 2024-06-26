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

use Fan\ElasticBoolQuery\Concerns\BuildClient;

abstract class Document implements DocumentInterface
{
    use BuildClient;

    public static function query(): Builder
    {
        return new Builder(new static());
    }
}
