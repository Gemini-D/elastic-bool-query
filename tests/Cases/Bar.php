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

namespace HyperfTest\Cases;

use Fan\ElasticBoolQuery\Config;
use Fan\ElasticBoolQuery\Document;

class Bar extends Document
{
    public function getIndex(): string
    {
        return 'bar';
    }

    public function getConfig(): Config
    {
        return new Config(['127.0.0.1:9200']);
    }
}
