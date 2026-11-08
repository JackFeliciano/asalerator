<?php

declare(strict_types=1);

use Asalerator\Home\Affiliate;
use Asalerator\Home\Business;
use Asalerator\Home\Network;
use Asalerator\Home\Offer;
use Asalerator\Tests\Assert;

function asa_network(): Network
{
    $network = new Network();
    $network->addBusiness(new Business('taberna', 'Taberna do Largo', 'commerce'));
    $network->addAffiliate(new Affiliate('ana', 'Ana Reis', null, 1));
    $network->addAffiliate(new Affiliate('bright', 'Bright Faria', 'ana', 2));
    $network->addAffiliate(new Affiliate('kestrel', 'Kestrel Dias', 'bright', 3));
    $network->listOffer(new Offer('tab-dinner', 'taberna', 'Dinner for two', 6000, 3000, [1000, 500, 250], '2026-11-30'));

    return $network;
}

return [
    ['a business names a handle, a sector and a name', static function (): void {
        Assert::throws(static fn () => new Business(' ', 'Taberna', 'commerce'), 'carries a handle');
        Assert::throws(static fn () => new Business('tab', ' ', 'commerce'), 'carries a name');
        Assert::throws(static fn () => new Business('tab', 'Taberna', 'mining'), 'industry, commerce or services');
    }],
    ['an affiliate at the first tier carries no sponsor', static function (): void {
        Assert::throws(static fn () => new Affiliate('ana', 'Ana', 'bright', 1), 'carries no sponsor');
        Assert::throws(static fn () => new Affiliate('bright', 'Bright', null, 2), 'names a sponsor');
        Assert::throws(static fn () => new Affiliate(' ', 'Bright', null, 1), 'carries a handle');
    }],
    ['an offer refuses a discount of the whole price and an empty tier list', static function (): void {
        Assert::throws(static fn () => new Offer('o', 'taberna', 'Dinner', 6000, 10000, [1000], '2026-11-30'), 'between nothing and the whole price');
        Assert::throws(static fn () => new Offer('o', 'taberna', 'Dinner', 6000, 0, [], '2026-11-30'), 'at least one commission tier');
        Assert::throws(static fn () => new Offer('o', 'taberna', 'Dinner', 6000, 0, [0], '2026-11-30'), 'between one and 10000');
        Assert::throws(static fn () => new Offer('o', 'taberna', ' ', 6000, 0, [1000], '2026-11-30'), 'carries a title');
    }],
    ['an offer sets the price an affiliate pays below the listed price', static function (): void {
        $offer = asa_network()->offer('tab-dinner');
        Assert::same(6000, $offer->price);
        Assert::same(4200, $offer->affiliatePrice());
        Assert::same(3, $offer->depth());
        Assert::same(500, $offer->share(2));
        Assert::same(0, $offer->share(4));
    }],
    ['a network refuses a repeated handle', static function (): void {
        $network = asa_network();
        Assert::throws(static fn () => $network->addBusiness(new Business('taberna', 'Taberna', 'commerce')), 'already holds taberna');
        Assert::throws(static fn () => $network->addAffiliate(new Affiliate('ana', 'Ana', null, 1)), 'already holds ana');
        Assert::throws(static fn () => $network->listOffer(new Offer('tab-dinner', 'taberna', 'Dinner', 6000, 0, [1000], '2026-11-30')), 'already holds tab-dinner');
    }],
    ['an affiliate joins under a sponsor already in the network', static function (): void {
        $network = new Network();
        Assert::throws(static fn () => $network->addAffiliate(new Affiliate('bright', 'Bright', 'ana', 2)), 'sponsor must already sit');
    }],
    ['an offer names a business in the network', static function (): void {
        $network = new Network();
        Assert::throws(static fn () => $network->listOffer(new Offer('o', 'nobody', 'Dinner', 6000, 0, [1000], '2026-11-30')), 'business in this network');
    }],
    ['a sale pays the affiliate price into the network', static function (): void {
        $network = asa_network();
        $sale = $network->sell('tab-dinner', 'ana', '2026-11-02');
        Assert::same(4200, $sale->amount);
        Assert::same(1, count($network->sales()));
    }],
    ['a sale after the closing day lands nowhere', static function (): void {
        $network = asa_network();
        Assert::throws(static fn () => $network->sell('tab-dinner', 'ana', '2026-12-01'), 'the offer closed on 2026-11-30');
    }],
    ['a sale by an affiliate outside the network lands nowhere', static function (): void {
        $network = asa_network();
        Assert::throws(static fn () => $network->sell('tab-dinner', 'nobody', '2026-11-02'), 'no affiliate called nobody');
    }],
    ['a sale names an offer the network holds', static function (): void {
        $network = asa_network();
        Assert::throws(static fn () => $network->sell('tab-nothing', 'ana', '2026-11-02'), 'no offer called tab-nothing');
    }],
    ['an affiliate earns the share at their own depth', static function (): void {
        $network = asa_network();
        $network->sell('tab-dinner', 'ana', '2026-11-02');
        Assert::same(420, $network->earned('ana'));
    }],
    ['a sponsor earns the share one tier above the seller', static function (): void {
        $network = asa_network();
        $network->sell('tab-dinner', 'bright', '2026-11-02');
        Assert::same(210, $network->earned('bright'));
        Assert::same(420, $network->earned('ana'));
    }],
    ['an affiliate two tiers above the seller earns nothing on that sale', static function (): void {
        $network = asa_network();
        $network->sell('tab-dinner', 'kestrel', '2026-11-02');
        Assert::same(105, $network->earned('kestrel'));
        Assert::same(210, $network->earned('bright'));
        Assert::same(0, $network->earned('ana'));
    }],
    ['an affiliate with no sale and no team earns nothing', static function (): void {
        Assert::same(0, asa_network()->earned('ana'));
    }],
    ['a network answers an unknown affiliate with nothing', static function (): void {
        Assert::throws(static fn () => asa_network()->earned('nobody'), 'no affiliate called nobody');
    }],
    ['an offer closes on its closing day and no later', static function (): void {
        $offer = asa_network()->offer('tab-dinner');
        Assert::same(true, $offer->isOpenOn('2026-11-30'));
        Assert::same(false, $offer->isOpenOn('2026-12-01'));
    }],
    ['a sale carries the day it happened', static function (): void {
        $sale = asa_network()->sell('tab-dinner', 'ana', '2026-11-02');
        Assert::same('2026-11-02', $sale->day);
        Assert::same('ana', $sale->affiliate);
    }],
    ['a sale refuses a day written any other way', static function (): void {
        $network = asa_network();
        Assert::throws(static fn () => $network->sell('tab-dinner', 'ana', '2 November'), 'day as 2026-01-31');
    }],
    ['a sale of an offer with one tier pays that tier only', static function (): void {
        $network = new Network();
        $network->addBusiness(new Business('taberna', 'Taberna', 'commerce'));
        $network->addAffiliate(new Affiliate('ana', 'Ana', null, 1));
        $network->addAffiliate(new Affiliate('bright', 'Bright', 'ana', 2));
        $network->listOffer(new Offer('tab-lunch', 'taberna', 'Lunch', 3000, 1000, [1000], '2026-11-30'));
        Assert::throws(static fn () => new Offer('tab-lunch', 'taberna', 'Lunch', 3000, 0, [], '2026-11-30'), 'at least one commission tier');
    }],
];
