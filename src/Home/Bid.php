<?php

declare(strict_types=1);

namespace Asalerator\Home;

/** One offer from one buyer, on one day, in whole cents. */
final class Bid
{
    public function __construct(
        public readonly string $buyer,
        public readonly int $amount,
        public readonly string $day,
    ) {
        if (trim($buyer) === '') {
            throw new \RuntimeException('a bid names its buyer');
        }
        if ($amount < 1) {
            throw new \RuntimeException('a bid carries a whole number of cents above zero');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
            throw new \RuntimeException('a bid carries its day as 2026-01-31');
        }
    }
}
