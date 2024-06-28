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
use Fan\ElasticBoolQuery\Exception\RuntimeException;
use Hyperf\Collection\Collection;

class Builder
{
    public array $where = [];

    public int $size = 10;

    public int $from = 0;

    public function __construct(protected DocumentInterface $document)
    {
    }

    public function where(string $key, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = [
            $key, $operator, $value,
        ];

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
        $bool = [];
        foreach ($this->where as [$key, $operator, $value]) {
            $operator = Operator::from($operator);

            $bool['must'][] = $operator->buildQuery($key, $value);
        }

        return [
            'query' => [
                'bool' => $bool,
            ],
            'size' => $this->size,
            'from' => $this->from,
        ];
    }

    public function update(array $doc, mixed $id = null): bool
    {
        $id ??= $doc[$this->document->getKey()] ?? $this->getKeyValue();

        if ($id === null) {
            throw new RuntimeException('The document does not contain any ID');
        }

        return $this->document->getClient()->update([
            'index' => $this->document->getIndex(),
            'id' => $id,
            'body' => [
                'doc' => $doc,
                'doc_as_upsert' => true,
            ],
            'refresh' => true,
            'retry_on_conflict' => 5,
        ])->asBool();
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
        return $this->document->getClient()->search([
            'index' => $this->document->getIndex(),
            'body' => $this->toBody(),
        ]);
    }

    protected function getKeyValue(): mixed
    {
        foreach ($this->where as [$key, $operator, $value]) {
            if ($key === $this->document->getKey() && $operator === '=') {
                return $value;
            }
        }

        return null;
    }
}
