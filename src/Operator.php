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
    case NOT_EQUAL = '!=';
    case NOT_EQUAL2 = '<>';
    case WILDCARD = 'wildcard';
    case LIKE = 'like';
    case GT_SYMBOL = '>';
    case GT = 'gt';
    case GTE_SYMBOL = '>=';
    case GTE = 'gte';
    case LT_SYMBOL = '<';
    case LT = 'lt';
    case LTE = 'lte';
    case LTE_SYMBOL = '<=';
    case IN = 'in';
    case TERMS = 'terms';

    public function getTag(): string
    {
        return match ($this) {
            self::NOT_EQUAL, self::NOT_EQUAL2 => 'must_not',
            default => 'must'
        };
    }

    public function buildQuery(string $key, mixed $value): array
    {
        return match ($this) {
            self::TERM, self::EQUAL, self::NOT_EQUAL, self::NOT_EQUAL2 => [
                'term' => [
                    $key => $value,
                ],
            ],
            self::WILDCARD, self::LIKE => [
                'wildcard' => [
                    $key => [
                        'value' => $value
                    ],
                ],
            ],
            self::GT_SYMBOL, self::GT => [
                'range' => [$key => ['gt' => $value]],
            ],
            self::LT_SYMBOL, self::LT => [
                'range' => [$key => ['lt' => $value]],
            ],
            self::GTE_SYMBOL, self::GTE => [
                'range' => [$key => ['gte' => $value]],
            ],
            self::LTE_SYMBOL, self::LTE => [
                'range' => [$key => ['lte' => $value]],
            ],
            self::IN, self::TERMS => ['terms' => [$key => $value]]
        };
    }

    public function isEqual(): bool
    {
        return $this === self::TERM || $this === self::EQUAL;
    }
}
