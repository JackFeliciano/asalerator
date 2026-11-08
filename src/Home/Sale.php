<?php

declare(strict_types=1);

namespace Asalerator\Home;

/** One offer taken off the market, by one affiliate, on one day, in whole cents. */
final class Sale
{
    public function __construct(
        public readonly string $offer,
        public readonly string $affiliate,
        public readonly int $amount,
        public readonly string $day,
    ) {
        if (trim($offer) === '') {
            throw new \RuntimeException('a sale names the offer it moves');
        }
        if (trim($affiliate) === '') {
            throw new \RuntimeException('a sale names the affiliate who made it');
        }
        if ($amount < 1) {
            throw new \RuntimeException('a sale carries a whole number of cents above zero');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
            throw new \RuntimeException('a sale carries its day as 2026-01-31');
        }
    }
}
