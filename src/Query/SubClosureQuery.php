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

use Fan\ElasticBoolQuery\Builder;

class SubClosureQuery implements SubQueryInterface
{
    public array $query = [];

    public function __construct(Builder $builder, public string $tag)
    {
        $this->query = $builder->toBool();
    }

    public function buildQuery(): array
    {
        return [
            'bool' => $this->query,
        ];
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
