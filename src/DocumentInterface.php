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

interface DocumentInterface
{
    public function getIndex(): string;

    public function getSearchIndex(): string;

    public function getConfig(): Config;

    public function getWriteClient(): Client;

    public function getReadClient(): Client;

    public function getKey(): string;

    public function getMapping(): array;
}
