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
use Fan\ElasticBoolQuery\Config;
use Fan\ElasticBoolQuery\Document;

require_once __DIR__ . '/../vendor/autoload.php';

class Foo extends Document
{
    public function getIndex(): string
    {
        return 'foo';
    }

    public function getConfig(): Config
    {
        return new Config(['127.0.0.1:9200']);
    }
}

$foo = new Foo();
$client = $foo->getClient();
$indices = $client->indices();

$exists = $indices->exists(['index' => 'foo']);
if (! $exists->asBool()) {
    $indices->create([
        'index' => $foo->getIndex(),
        'params' => [
            'settings' => [
                'number_of_shards' => 4,
            ],
        ],
    ]);
}

$indices->putMapping([
    'index' => $foo->getIndex(),
    'body' => [
        'properties' => [
            'id' => ['type' => 'long'],
            'name' => ['type' => 'keyword'],
            'summary' => ['type' => 'text'],
        ],
    ],
]);

$docs = [
    ['id' => 1, 'name' => 'foo', 'summary' => 'foo'],
    ['id' => 2, 'name' => 'limx', 'summary' => 'limx'],
    ['id' => 3, 'name' => 'leo', 'summary' => 'leo'],
    ['id' => 4, 'name' => 'fofo', 'summary' => 'fofo'],
    ['id' => 5, 'name' => 'lala', 'summary' => 'lala'],
];

foreach ($docs as $doc) {
    $client->update([
        'index' => $foo->getIndex(),
        'id' => $doc['id'],
        'body' => [
            'doc' => $doc,
            'doc_as_upsert' => true,
        ],
        'refresh' => true,
        'retry_on_conflict' => 5,
    ]);
}
