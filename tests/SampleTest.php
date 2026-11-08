<?php

declare(strict_types=1);

use Asalerator\Home\Sample;
use Asalerator\Tests\Assert;

return [
    ['the sample holds four affiliates in one network', static function (): void {
        $network = (new Sample())->network();
        Assert::same(1, $network->affiliate('ana')->depth);
        Assert::same(2, $network->affiliate('bright')->depth);
        Assert::same(3, $network->affiliate('kestrel')->depth);
        Assert::same(1, $network->affiliate('marchmont')->depth);
        Assert::same(null, $network->affiliate('marchmont')->sponsor);
    }],
    ['the sample holds four offers and three sales', static function (): void {
        $network = (new Sample())->network();
        Assert::same(3, $network->offer('freight-audit')->depth());
        Assert::same(2, $network->offer('freight-pallet')->depth());
        Assert::same(3, count($network->sales()));
    }],
    ['the freight audit pays a three tier chain', static function (): void {
        $network = (new Sample())->network();
        Assert::same(21600, $network->earned('ana'));
        Assert::same(5040, $network->earned('bright'));
        Assert::same(0, $network->earned('kestrel'));
        Assert::same(0, $network->earned('marchmont'));
    }],
    ['the dinner sale pays the seller and the sponsor', static function (): void {
        $network = (new Sample())->network();
        Assert::same(0, $network->earned('kestrel'));
        Assert::same(5040, $network->earned('bright'));
    }],
    ['the sample pays out what the three sales carry', static function (): void {
        $network = (new Sample())->network();
        $total = 0;
        foreach (['ana', 'bright', 'kestrel', 'marchmont'] as $handle) {
            $total += $network->earned($handle);
        }
        Assert::same(26640, $total);
    }],
    ['an unknown offer answers nothing', static function (): void {
        Assert::throws(static fn () => (new Sample())->network()->offer('offer-nothing'), 'no offer called');
    }],
];
