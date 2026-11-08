<?php

declare(strict_types=1);

namespace Asalerator\Home;

/**
 * One network on one server. Three businesses list four offers across three
 * tiers of commission, and four affiliates sell them.
 */
final class Sample
{
    private readonly Network $network;

    public function __construct()
    {
        $network = new Network();

        $network->addBusiness(new Business('porto-freight', 'Porto Freight', 'services'));
        $network->addBusiness(new Business('alvenaria', 'Alvenaria Lda', 'industry'));
        $network->addBusiness(new Business('taberna', 'Taberna do Largo', 'commerce'));

        $network->addAffiliate(new Affiliate('ana', 'Ana Reis', null, 1));
        $network->addAffiliate(new Affiliate('bright', 'Bright Faria', 'ana', 2));
        $network->addAffiliate(new Affiliate('kestrel', 'Kestrel Dias', 'bright', 3));
        $network->addAffiliate(new Affiliate('marchmont', 'Marchmont Costa', null, 1));

        $network->listOffer(new Offer('freight-audit', 'porto-freight', 'Freight audit, one quarter', 90000, 2000, [1500, 700, 300], '2026-11-30'));
        $network->listOffer(new Offer('freight-pallet', 'porto-freight', 'Pallet tracking, one year', 24000, 1500, [1200, 500], '2026-11-15'));
        $network->listOffer(new Offer('alv-survey', 'alvenaria', 'Structural survey', 120000, 2500, [1000, 400], '2026-11-30'));
        $network->listOffer(new Offer('tab-dinner', 'taberna', 'Dinner for two with wine', 6000, 3000, [1000], '2026-11-30'));

        $network->sell('freight-audit', 'ana', '2026-11-02');
        $network->sell('freight-audit', 'bright', '2026-11-04');
        $network->sell('tab-dinner', 'kestrel', '2026-11-05');

        $this->network = $network;
    }

    public function network(): Network
    {
        return $this->network;
    }
}
