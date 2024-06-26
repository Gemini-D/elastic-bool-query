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

use Fan\ElasticBoolQuery\Builder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BuilderTest extends TestCase
{
    public function testRawQuery()
    {
        $res = Foo::query()->rawSearch([
            'query' => [
                'bool' => [
                    'must' => [
                        ['range' => ['id' => ['gt' => 1]]],
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['id' => 2]],
                                    ['term' => ['id' => 5]],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame(2, count($res->asArray()['hits']['hits']));
    }

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

    public function testOrWhere()
    {
        $res = Foo::query()->orWhere('id', 1)->orWhere('id', 2)->orderBy('id', 'asc')->get();

        $this->assertSame(1, $res->first()['id']);
        $this->assertSame(2, $res->last()['id']);

        $this->assertSame(2, $res->count());
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

    public function testOrderBy()
    {
        $res = Foo::query()->orderBy('id', 'asc')->get();

        $this->assertSame(1, $res->first()['id']);

        $res = Foo::query()->where('id', '<=', 5)->orderBy('id', 'desc')->get();

        $this->assertSame(5, $res->first()['id']);
    }

    public function testNotEqual()
    {
        $res = Foo::query()->where('id', '!=', 1)->orderBy('id', 'asc')->get();

        $this->assertSame(2, $res->first()['id']);
    }

    public function testClosure()
    {
        $res = Foo::query()->where('id', '>', 1)
            ->where(function (Builder $builder) {
                $builder->where('id', 'in', [1, 2, 5]);
            })
            ->orderBy('id', 'asc')
            ->get();

        $this->assertSame(2, $res->count());
        $this->assertSame(2, $res->first()['id']);
        $this->assertSame(5, $res->last()['id']);
    }

    public function testOrWhereClosure()
    {
        $res = Foo::query()->where('id', '>', 1)
            ->where(function (Builder $builder) {
                $builder->orWhere('id', 1)
                    ->orWhere('id', 2)
                    ->orWhere('id', 5);
            })
            ->orderBy('id', 'asc')
            ->get();

        $this->assertSame(2, $res->count());
        $this->assertSame(2, $res->first()['id']);
        $this->assertSame(5, $res->last()['id']);
    }
}
