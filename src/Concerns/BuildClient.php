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

namespace Fan\ElasticBoolQuery\Concerns;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Fan\ElasticBoolQuery\Config;
use GuzzleHttp;

trait BuildClient
{
    public function getClient(Config $config): Client
    {
        return ClientBuilder::create()
            ->setHttpClient(new GuzzleHttp\Client())
            ->setHosts($config->getHosts())
            ->build();
    }
}
