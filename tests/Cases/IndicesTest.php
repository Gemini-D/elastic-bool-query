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

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class IndicesTest extends TestCase
{
    public function testExists()
    {
        $this->assertTrue(Foo::indices()->exists());
        $this->assertFalse(Bar::indices()->exists());
    }
}
