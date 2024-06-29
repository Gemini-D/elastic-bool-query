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

class Indices
{
    protected Builder $builder;

    public function __construct(public DocumentInterface $document)
    {
        $this->builder = new Builder($this->document);
    }

    public function exists(): bool
    {
        return $this->document->getClient()->indices()->exists(['index' => $this->document->getIndex()])->asBool();
    }
}
