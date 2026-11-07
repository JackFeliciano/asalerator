<?php

declare(strict_types=1);

use Asalerator\Home\Sample;
use Asalerator\Tests\Assert;

return [
    ['the sample holds three sales and five bids', static function (): void {
        $sample = new Sample();
        Assert::same(3, count($sample->sales()));
        Assert::same(5, $sample->bids());
    }],
    ['the press sale closes with a winner', static function (): void {
        $answer = (new Sample())->sale('sale-press')->answer();
        Assert::same('kestrel', $answer['buyer']);
        Assert::same(31000, $answer['standing']);
        Assert::same(27000, $answer['price']);
    }],
    ['the desk sale closes unsold', static function (): void {
        $answer = (new Sample())->sale('sale-desk')->answer();
        Assert::same(null, $answer['buyer']);
        Assert::same(0, $answer['price']);
    }],
    ['the lamp sale holds two bids from two buyers', static function (): void {
        $sale = (new Sample())->sale('sale-lamps');
        Assert::same(2, $sale->bids());
        Assert::same('marchmont', $sale->standing()->buyer);
        Assert::same(9250, $sale->answer()['price']);
    }],
    ['an unknown sale answers nothing', static function (): void {
        Assert::throws(static fn () => (new Sample())->sale('sale-nothing'), 'no sale called');
    }],
];
