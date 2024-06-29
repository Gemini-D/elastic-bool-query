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

namespace Fan\ElasticBoolQuery\Query;

use Fan\ElasticBoolQuery\Operator;

class SubQuery implements SubQueryInterface
{
    public function __construct(public string $key, public Operator $operator, public mixed $value, public string $boolean = 'and')
    {
    }

    public function buildQuery(): array
    {
        return $this->operator->buildQuery($this->key, $this->value);
    }

    public function getTag(): string
    {
        return match ($this->boolean) {
            'or' => match ($this->operator->getTag()) {
                'must_not' => 'should_not',
                default => 'should',
            },
            default => $this->operator->getTag()
        };
    }
}
