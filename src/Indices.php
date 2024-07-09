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

use JetBrains\PhpStorm\ArrayShape;

class Indices
{
    protected Builder $builder;

    public function __construct(public DocumentInterface $document)
    {
        $this->builder = new Builder($this->document);
    }

    public function exists(): bool
    {
        return $this->document->getReadClient()->indices()->exists(['index' => $this->document->getSearchIndex()])->asBool();
    }

    public function create(
        #[ArrayShape([
            'number_of_shards' => 'int',
        ])]
        ?array $settings = null
    ): bool {
        $settings ??= $this->document->getConfig()->getIndicesSettings();
        return $this->document->getWriteClient()
            ->indices()
            ->create([
                'index' => $this->document->getIndex(),
                'params' => [
                    'settings' => $settings,
                ],
            ])
            ->asBool();
    }

    public function delete(): bool
    {
        return $this->document->getWriteClient()
            ->indices()
            ->delete(['index' => $this->document->getIndex()])
            ->asBool();
    }

    public function putMapping(): bool
    {
        return $this->document->getWriteClient()
            ->indices()
            ->putMapping([
                'index' => $this->document->getIndex(),
                'body' => [
                    'properties' => $this->document->getMapping(),
                ],
            ])
            ->asBool();
    }

    public function getMapping(): array
    {
        return $this->document->getReadClient()
            ->indices()
            ->getMapping([
                'index' => $this->document->getSearchIndex(),
            ])
            ->asArray();
    }
}
