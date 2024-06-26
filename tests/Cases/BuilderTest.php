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
        Foo::query()->where('id', 1);

        $this->assertTrue(true);
    }
}

class Foo extends Document
{
    public function getIndex(): string
    {
        return 'foo';
    }
}
