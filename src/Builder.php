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

use Closure;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Fan\ElasticBoolQuery\Exception\RuntimeException;
use Fan\ElasticBoolQuery\Query\SubClosureQuery;
use Fan\ElasticBoolQuery\Query\SubQuery;
use Fan\ElasticBoolQuery\Query\SubQueryInterface;
use Hyperf\Collection\Collection;
use stdClass;

class Builder
{
    /**
     * @var SubQueryInterface[]
     */
    protected array $where = [];

    protected int $size = 10;

    protected int $from = 0;

    protected array $orderBy = [];

    public function __construct(protected DocumentInterface $document)
    {
    }

    public function where(Closure|string $key, mixed $operator = null, mixed $value = null): static
    {
        if (func_num_args() === 1 && is_callable($key)) {
            return $this->whereClosure($key);
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = new SubQuery($key, Operator::from($operator), $value, 'and');

        return $this;
    }

    public function orWhere(Closure|string $key, mixed $operator = null, mixed $value = null): static
    {
        if (func_num_args() === 1 && is_callable($key)) {
            return $this->whereClosure($key, 'should');
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = new SubQuery($key, Operator::from($operator), $value, 'or');

        return $this;
    }

    public function whereClosure(Closure $closure, string $tag = 'must'): static
    {
        $builder = new static($this->document);

        $closure($builder);

        $this->where[] = new SubClosureQuery($builder, $tag);

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

    public function orderBy(string $field, string $direction = 'ASC'): static
    {
        $this->orderBy[] = [$field => $direction];
        return $this;
    }

    public function toBool(): array
    {
        $bool = [];
        foreach ($this->where as $subQuery) {
            $bool[$subQuery->getTag()][] = $subQuery->buildQuery();
        }

        if (! $bool) {
            $bool['must'][] = ['match_all' => new stdClass()];
        }

        return $bool;
    }

    public function toBody(): array
    {
        $body = [];
        if ($this->orderBy) {
            $body['sort'] = $this->orderBy;
        }

        return [
            'query' => [
                'bool' => $this->toBool(),
            ],
            'size' => $this->size,
            'from' => $this->from,
            ...$body,
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

    public function rawSearch(array $body): Elasticsearch
    {
        return $this->document->getClient()->search([
            'index' => $this->document->getIndex(),
            'body' => $body,
        ]);
    }

    protected function getKeyValue(): mixed
    {
        foreach ($this->where as $subQuery) {
            if ($subQuery instanceof SubQuery) {
                if ($subQuery->key === $this->document->getKey() && $subQuery->operator->isEqual()) {
                    return $subQuery->value;
                }
            }
        }

        return null;
    }
}
