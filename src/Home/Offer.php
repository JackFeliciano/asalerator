<?php

declare(strict_types=1);

namespace Asalerator\Home;

/**
 * One product or service a business lists for affiliates to move. The tiers hold
 * the share in basis points an affiliate earns at each depth of the network.
 */
final class Offer
{
    /** @param list<int> $tiers */
    public function __construct(
        public readonly string $handle,
        public readonly string $business,
        public readonly string $title,
        public readonly int $price,
        public readonly int $discount,
        public readonly array $tiers,
        public readonly string $closesOn,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('an offer carries a handle');
        }
        if (trim($title) === '') {
            throw new \RuntimeException('an offer carries a title');
        }
        if ($price < 1) {
            throw new \RuntimeException('an offer carries a price above zero cents');
        }
        if ($discount < 0 || $discount >= 10000) {
            throw new \RuntimeException('an affiliate discount sits between nothing and the whole price');
        }
        if ($tiers === []) {
            throw new \RuntimeException('an offer carries at least one commission tier');
        }
        foreach ($tiers as $share) {
            if ($share < 1 || $share > 10000) {
                throw new \RuntimeException('a commission tier sits between one and 10000 basis points');
            }
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $closesOn)) {
            throw new \RuntimeException('an offer carries its closing day as 2026-01-31');
        }
    }

    public function affiliatePrice(): int
    {
        return $this->price - intdiv($this->price * $this->discount, 10000);
    }

    public function depth(): int
    {
        return count($this->tiers);
    }

    public function share(int $level): int
    {
        return $this->tiers[$level - 1] ?? 0;
    }

    public function isOpenOn(string $day): bool
    {
        return $day <= $this->closesOn;
    }
}
