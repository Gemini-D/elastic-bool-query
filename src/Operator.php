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

enum Operator: string
{
    case TERM = 'term';
    case EQUAL = '=';
    case WILDCARD = 'wildcard';
    case LIKE = 'like';

    public function buildQuery(string $key, mixed $value): array
    {
        return match ($this) {
            self::TERM, self::EQUAL => [
                'term' => [
                    $key => $value,
                ],
            ],
            self::WILDCARD, self::LIKE => [
                'wildcard' => [
                    $key => $value,
                ],
            ],
        };
    }
}
