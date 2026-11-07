<?php

declare(strict_types=1);

namespace Asalerator\Home;

/**
 * One item offered between two days. The sale keeps the standing bid and the bid
 * that stood before it, because the winner pays one step over the second.
 */
final class Sale
{
    /** @var array<string, Bid> */
    private array $bids = [];

    private ?Bid $standing = null;
    private ?Bid $previous = null;

    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        public readonly int $opening,
        public readonly int $step,
        public readonly string $closingDay,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a sale carries a handle');
        }
        if (trim($title) === '') {
            throw new \RuntimeException('a sale carries a title');
        }
        if ($opening < 1) {
            throw new \RuntimeException('a sale opens above zero cents');
        }
        if ($step < 1) {
            throw new \RuntimeException('a sale carries a step above zero cents');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $closingDay)) {
            throw new \RuntimeException('a sale carries its closing day as 2026-01-31');
        }
    }

    public function place(Bid $bid): void
    {
        if ($bid->day > $this->closingDay) {
            throw new \RuntimeException('the sale closed on ' . $this->closingDay);
        }
        if ($bid->amount < $this->opening) {
            throw new \RuntimeException('a bid below the opening price lands nowhere');
        }
        if ($this->standing !== null) {
            if ($bid->amount < $this->standing->amount + $this->step) {
                throw new \RuntimeException('a bid beats the standing bid by one step of ' . $this->step . ' cents');
            }
            if ($bid->amount === $this->standing->amount) {
                throw new \RuntimeException('a bid level with the standing bid lands nowhere');
            }
        }
        $this->bids[$bid->buyer] = $bid;
        $this->previous = $this->standing;
        $this->standing = $bid;
    }

    public function standing(): ?Bid
    {
        return $this->standing;
    }

    public function bids(): int
    {
        return count($this->bids);
    }

    /** @return array{buyer: ?string, price: int, standing: int, bids: int, closed: string} */
    public function answer(): array
    {
        if ($this->standing === null) {
            return ['buyer' => null, 'price' => 0, 'standing' => 0, 'bids' => 0, 'closed' => $this->closingDay];
        }
        $floor = $this->previous?->amount ?? ($this->opening - $this->step);

        return [
            'buyer' => $this->standing->buyer,
            'price' => $floor + $this->step,
            'standing' => $this->standing->amount,
            'bids' => $this->bids(),
            'closed' => $this->closingDay,
        ];
    }
}
