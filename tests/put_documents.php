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

    public function getMapping(): array
    {
        return [
            'id' => ['type' => 'long'],
            'name' => ['type' => 'keyword'],
            'summary' => ['type' => 'text'],
        ];
    }
}

$foo = new Foo();
$client = $foo->getWriteClient();
$indices = $client->indices();
$indices = Foo::indices();

if ($indices->exists()) {
    $indices->delete();
}
$indices->create(['number_of_shards' => 4]);

$indices->putMapping();

$docs = [
    ['id' => 1, 'name' => 'foo', 'summary' => 'foo'],
    ['id' => 2, 'name' => 'limx', 'summary' => 'limx'],
    ['id' => 3, 'name' => 'leo', 'summary' => 'leo'],
    ['id' => 4, 'name' => 'fofo', 'summary' => 'fofo'],
    ['id' => 5, 'name' => 'lala', 'summary' => 'lala'],
];

foreach ($docs as $doc) {
    Foo::query()->update($doc, $doc['id']);
}

$res = Foo::query()->bulk([
    ['id' => 6, 'name' => 'elastic', 'summary' => uniqid()],
    ['id' => 7, 'name' => 'elastic2', 'summary' => uniqid()],
    ['id' => 8, 'name' => '我是中文', 'summary' => '我是中文'],
    ['id' => 9, 'name' => '我是中文啊', 'summary' => '我是中文啊'],
]);

var_dump($res);
