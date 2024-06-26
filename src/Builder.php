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

use Elastic\Elasticsearch\Response\Elasticsearch;
use Hyperf\Collection\Collection;

class Builder
{
    public array $bool = [];

    public int $size = 10;

    public int $from = 0;

    public function __construct(protected DocumentInterface $index)
    {
    }

    public function where(string $key, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $operator = Operator::from($operator);

        $this->bool['must'][] = $operator->buildQuery($key, $value);

        return $this;
    }

    public function from(int $from): static
    {
        $this->from = $from;
        return $this;
    }

    public function size(int $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function toBody(): array
    {
        return [
            'query' => [
                'bool' => $this->bool,
            ],
            'size' => $this->size,
            'from' => $this->from,
        ];
    }

    public function get(): Collection
    {
        $response = $this->search()->asArray();

        $result = [];
        foreach ($response['hits']['hits'] as $hit) {
            $result[] = $hit['_source'];
        }
        return new Collection($result);
    }

    public function search(): Elasticsearch
    {
        return $this->index->getClient()->search([
            'index' => $this->index->getIndex(),
            'body' => $this->toBody(),
        ]);
    }
}
