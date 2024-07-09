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

        $this->assertSame(['id' => 1, 'name' => 'foo', 'summary' => 'foo'], $res->first()->source);
    }

    public function testGetArrayAccess()
    {
        $res = Foo::query()->where('id', 1)->get()->first();

        $this->assertSame('1', $res['_id']);
        $this->assertSame('foo', $res['name']);
        $this->assertSame('foo', $res['summary']);
        $this->assertSame(['_id' => '1', 'id' => 1, 'name' => 'foo', 'summary' => 'foo'], $res->toArray());
        $this->assertSame(json_encode(['_id' => '1', 'id' => 1, 'name' => 'foo', 'summary' => 'foo']), json_encode($res));
    }

    public function testLikeChinese()
    {
        $res = Foo::query()->where('summary', 'like', '*中*')->get()->first();
        $this->assertSame(8, $res['id']);

        $res = Foo::query()->where('name', 'like', '*中文*')->get()->first();
        $this->assertSame(8, $res['id']);

        $res = Foo::query()->where('summary', 'match', '中文')->get()->first();
        $this->assertSame(8, $res['id']);
    }

    public function testOrWhere()
    {
        $res = Foo::query()->orWhere('id', 1)->orWhere('id', 2)->orderBy('id', 'asc')->get();

        $this->assertSame(1, $res->first()->source['id']);
        $this->assertSame(2, $res->last()->source['id']);

        $this->assertSame(2, $res->count());
    }

    public function testLike()
    {
        $res = Foo::query()->where('summary', 'like', '*fo*')->get();

        $this->assertSame(2, $res->count());
    }

    public function testPaginate()
    {
        [$total, $data] = Foo::query()->where('summary', 'like', '*o*')->paginate();

        $this->assertSame(3, $data->count());
        $this->assertSame(3, $total);
    }

    public function testUpdateAndGet()
    {
        $res = Foo::query()->where('id', 1)->get();

        $summary = $res->first()->source['summary'];

        Foo::query()->where('id', 1)->update(['summary' => 'foofoo']);

        $res = Foo::query()->where('id', 1)->get();

        $this->assertSame('foofoo', $res->first()->source['summary']);

        Foo::query()->where('id', 1)->update(['summary' => $summary]);
    }

    public function testLTAndGT()
    {
        $res = Foo::query()->where('id', '>', 4)->get();
        $this->assertSame(5, $res->count());

        $res = Foo::query()->where('id', '>=', 4)->where('id', '<', 8)->get();
        $this->assertSame(4, $res->count());
    }

    public function testTerms()
    {
        $res = Foo::query()->where('id', 'in', [1, 2])->get();

        $this->assertSame(2, $res->count());
    }

    public function testOrderBy()
    {
        $res = Foo::query()->orderBy('id', 'asc')->get();

        $this->assertSame(1, $res->first()->source['id']);

        $res = Foo::query()->where('id', '<=', 5)->orderBy('id', 'desc')->get();

        $this->assertSame(5, $res->first()->source['id']);
    }

    public function testNotEqual()
    {
        $res = Foo::query()->where('id', '!=', 1)->orderBy('id', 'asc')->get();

        $this->assertSame(2, $res->first()->source['id']);
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
        $this->assertSame(2, $res->first()->source['id']);
        $this->assertSame(5, $res->last()->source['id']);
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
        $this->assertSame(2, $res->first()->source['id']);
        $this->assertSame(5, $res->last()->source['id']);
    }

    public function testBulk()
    {
        $res = Foo::query()->bulk([
            ['id' => 6, 'name' => 'elastic', 'summary' => $id = uniqid()],
            ['id' => 7, 'name' => 'elastic2', 'summary' => $id2 = uniqid()],
        ]);

        $this->assertTrue($res);

        $first = Foo::query()->where('id', 6)->get()->first()->source;
        $this->assertSame($id, $first['summary']);

        $first = Foo::query()->where('id', 7)->get()->first()->source;
        $this->assertSame($id2, $first['summary']);
    }
}
