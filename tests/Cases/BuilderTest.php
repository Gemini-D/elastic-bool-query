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

        $this->assertSame(['id' => 1, 'name' => 'foo', 'summary' => 'foo'], $res->first());
    }

    public function testLike()
    {
        $res = Foo::query()->where('summary', 'like', '*o*')->get();

        $this->assertSame(3, $res->count());
    }

    public function testUpdateAndGet()
    {
        $res = Foo::query()->where('id', 1)->get();

        $summary = $res->first()['summary'];

        Foo::query()->where('id', 1)->update(['summary' => 'foofoo']);

        $res = Foo::query()->where('id', 1)->get();

        $this->assertSame('foofoo', $res->first()['summary']);

        Foo::query()->where('id', 1)->update(['summary' => $summary]);
    }

    public function testLTAndGT()
    {
        $res = Foo::query()->where('id', '>', 4)->get();
        $this->assertSame(1, $res->count());

        $res = Foo::query()->where('id', '>=', 4)->get();
        $this->assertSame(2, $res->count());
    }

    public function testTerms()
    {
        $res = Foo::query()->where('id', 'in', [1, 2])->get();

        $this->assertSame(2, $res->count());
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
