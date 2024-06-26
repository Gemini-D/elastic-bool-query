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

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use GuzzleHttp;
use Hyperf\Collection\Collection;

class Builder
{
    public array $bool = [];

    public array $operators = [
        '=' => 'term',
    ];

    public function __construct(protected DocumentInterface $index)
    {
    }

    public function where(string $key, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $operator = $this->operators[$operator] ?? $operator;

        $this->bool['must'][] = [
            $operator => [$key => $value],
        ];

        return $this;
    }

    public function get(): Collection
    {
        $response = $this->client()->search([
            'index' => $this->index->getIndex(),
            'body' => [
                'query' => [
                    'bool' => $this->bool,
                ],
            ],
        ]);

        return new Collection($response->asArray());
    }

    public function client(): Client
    {
        return ClientBuilder::create()
            ->setHttpClient(new GuzzleHttp\Client())
            ->setHosts($this->index->getConfig()->getHosts())
            ->build();
    }
}
