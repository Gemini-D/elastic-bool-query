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
use Fan\ElasticBoolQuery\Exception\RuntimeException;
use GuzzleHttp;

use function Hyperf\Config\config;

abstract class Document implements DocumentInterface
{
    public static function query(): Builder
    {
        return new Builder(new static());
    }

    public function getKey(): string
    {
        return 'id';
    }

    public function getConfig(): Config
    {
        if (function_exists('Hyperf\Config\config')) {
            $config = config('elastic_bool_query', ['hosts' => ['127.0.0.1:9200']]);
            return new Config($config['hosts']);
        }

        throw new RuntimeException('You must rewrite `getConfig()` for your documents.');
    }

    public function getClient(): Client
    {
        return ClientBuilder::create()
            ->setHttpClient(new GuzzleHttp\Client())
            ->setHosts($this->getConfig()->getHosts())
            ->build();
    }
}
