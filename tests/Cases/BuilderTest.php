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
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BuilderTest extends TestCase
{
    public function testWhere()
    {
        $body = Foo::query()->where('id', 1)->from(1)->size(5)->toBody();

        $this->assertSame(
            [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['id' => 1]],
                        ],
                    ],
                ],
                'size' => 5,
                'from' => 1,
            ],
            $body
        );
    }

    public function testGet()
    {
        $res = Foo::query()->where('id', 1)->get();

        $this->assertSame(['id' => 1, 'name' => 'foo'], $res->first());
    }
}

class Foo extends Document
{
    public function getIndex(): string
    {
        return 'foo';
    }

    public function getConfig(): Config
    {
        return new Config(['127.0.0.1:9200']);
    }
}
