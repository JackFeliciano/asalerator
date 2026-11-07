<?php

declare(strict_types=1);

namespace Asalerator\Home;

/**
 * Three sales on one server. The first closes with a winner and a price one step
 * over the second bid, the second closes unsold, and the third takes no more bid.
 */
final class Sample
{
    /** @var list<Sale> */
    private array $sales = [];

    public function __construct()
    {
        $press = new Sale('sale-press', 'A letterpress, 1962', 24000, 1000, '2026-10-10');
        $press->place(new Bid('ana', 24000, '2026-10-02'));
        $press->place(new Bid('bright', 26000, '2026-10-03'));
        $press->place(new Bid('kestrel', 31000, '2026-10-08'));
        $this->sales[] = $press;

        $desk = new Sale('sale-desk', 'A walnut desk', 18000, 500, '2026-10-12');
        $this->sales[] = $desk;

        $lamps = new Sale('sale-lamps', 'Four brass lamps', 9000, 250, '2026-10-05');
        $lamps->place(new Bid('ana', 9000, '2026-10-01'));
        $lamps->place(new Bid('marchmont', 9500, '2026-10-04'));
        $this->sales[] = $lamps;
    }

    /** @return list<Sale> */
    public function sales(): array
    {
        return $this->sales;
    }

    public function sale(string $handle): Sale
    {
        foreach ($this->sales as $sale) {
            if ($sale->handle === $handle) {
                return $sale;
            }
        }

        throw new \RuntimeException('this server holds no sale called ' . $handle);
    }

    public function bids(): int
    {
        $sum = 0;
        foreach ($this->sales as $sale) {
            $sum += $sale->bids();
        }

        return $sum;
    }
}
