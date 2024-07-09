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

use ArrayAccess;
use Fan\ElasticBoolQuery\Exception\RuntimeException;
use Hyperf\Contract\Arrayable;
use JsonSerializable;

class Value implements JsonSerializable, Arrayable, ArrayAccess
{
    public function __construct(public mixed $id, public array $source)
    {
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return array_merge(['_id' => $this->id], $this->source);
    }

    public function offsetExists(mixed $offset): bool
    {
        if ($offset === '_id') {
            return true;
        }

        return isset($this->source[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $result = $this->source[$offset] ?? null;
        if ($result === null && $offset === '_id') {
            return $this->id;
        }

        return $result;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (array_key_exists($offset, $this->source)) {
            $this->source[$offset] = $value;
        } elseif ($offset === '_id') {
            $this->id = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if (array_key_exists($offset, $this->source)) {
            unset($this->source[$offset]);
        } elseif ($offset === '_id') {
            throw new RuntimeException('Cannot unset _id from `Value`');
        }
    }
}
