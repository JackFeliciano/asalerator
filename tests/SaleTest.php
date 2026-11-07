<?php

declare(strict_types=1);

use Asalerator\Home\Bid;
use Asalerator\Home\Sale;
use Asalerator\Tests\Assert;

function asa_sale(): Sale
{
    return new Sale('sale-press', 'A letterpress', 24000, 1000, '2026-10-10');
}

return [
    ['a bid names a buyer, an amount and a day', static function (): void {
        Assert::throws(static fn () => new Bid(' ', 24000, '2026-10-01'), 'names its buyer');
        Assert::throws(static fn () => new Bid('ana', 0, '2026-10-01'), 'above zero');
        Assert::throws(static fn () => new Bid('ana', 24000, '1 October'), 'day as 2026-01-31');
    }],
    ['a sale carries a handle, a title, an opening price, a step and a closing day', static function (): void {
        Assert::throws(static fn () => new Sale(' ', 'A title', 24000, 1000, '2026-10-10'), 'carries a handle');
        Assert::throws(static fn () => new Sale('h', ' ', 24000, 1000, '2026-10-10'), 'carries a title');
        Assert::throws(static fn () => new Sale('h', 'A title', 0, 1000, '2026-10-10'), 'above zero cents');
        Assert::throws(static fn () => new Sale('h', 'A title', 24000, 0, '2026-10-10'), 'step above zero');
        Assert::throws(static fn () => new Sale('h', 'A title', 24000, 1000, '10/10/2026'), 'closing day');
    }],
    ['a bid below the opening price lands nowhere', static function (): void {
        $sale = asa_sale();
        Assert::throws(static fn () => $sale->place(new Bid('ana', 23000, '2026-10-01')), 'below the opening price');
        Assert::same(0, $sale->bids());
    }],
    ['a bid at the opening price stands', static function (): void {
        $sale = asa_sale();
        $sale->place(new Bid('ana', 24000, '2026-10-01'));
        Assert::same('ana', $sale->standing()->buyer);
    }],
    ['a bid must beat the standing bid by one step', static function (): void {
        $sale = asa_sale();
        $sale->place(new Bid('ana', 24000, '2026-10-01'));
        Assert::throws(static fn () => $sale->place(new Bid('bright', 24500, '2026-10-02')), 'one step of 1000 cents');
        $sale->place(new Bid('bright', 25000, '2026-10-02'));
        Assert::same('bright', $sale->standing()->buyer);
    }],
    ['a bid level with the standing bid lands nowhere', static function (): void {
        $sale = asa_sale();
        $sale->place(new Bid('ana', 24000, '2026-10-01'));
        Assert::throws(static fn () => $sale->place(new Bid('bright', 24000, '2026-10-02')), 'one step');
    }],
    ['a sale takes no bid after its closing day', static function (): void {
        $sale = asa_sale();
        $sale->place(new Bid('ana', 24000, '2026-10-10'));
        Assert::throws(static fn () => $sale->place(new Bid('bright', 26000, '2026-10-11')), 'the sale closed on 2026-10-10');
    }],
    ['a buyer may bid twice and the later bid stands', static function (): void {
        $sale = asa_sale();
        $sale->place(new Bid('ana', 24000, '2026-10-01'));
        $sale->place(new Bid('ana', 26000, '2026-10-02'));
        Assert::same(1, $sale->bids());
        Assert::same(26000, $sale->standing()->amount);
    }],
    ['the winner pays one step over the bid that stood before', static function (): void {
        $sale = asa_sale();
        $sale->place(new Bid('ana', 24000, '2026-10-01'));
        $sale->place(new Bid('bright', 26000, '2026-10-02'));
        $sale->place(new Bid('kestrel', 31000, '2026-10-08'));
        $answer = $sale->answer();
        Assert::same('kestrel', $answer['buyer']);
        Assert::same(31000, $answer['standing']);
        Assert::same(27000, $answer['price']);
    }],
    ['the first bid pays one step over the opening price less a step', static function (): void {
        $sale = asa_sale();
        $sale->place(new Bid('ana', 26000, '2026-10-01'));
        Assert::same(24000, $sale->answer()['price']);
    }],
    ['a sale with no bid answers no winner and a price of zero', static function (): void {
        $answer = asa_sale()->answer();
        Assert::same(null, $answer['buyer']);
        Assert::same(0, $answer['price']);
        Assert::same(0, $answer['bids']);
    }],
];
