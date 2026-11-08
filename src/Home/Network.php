<?php

declare(strict_types=1);

namespace Asalerator\Home;

/**
 * One network on one server. It keeps the businesses, the offers, the affiliates
 * and the sales, and it answers what each affiliate earned and at which depth.
 */
final class Network
{
    /** @var array<string, Business> */
    private array $businesses = [];

    /** @var array<string, Offer> */
    private array $offers = [];

    /** @var array<string, Affiliate> */
    private array $affiliates = [];

    /** @var list<Sale> */
    private array $sales = [];

    public function addBusiness(Business $business): void
    {
        if (isset($this->businesses[$business->handle])) {
            throw new \RuntimeException('this network already holds ' . $business->handle);
        }
        $this->businesses[$business->handle] = $business;
    }

    public function addAffiliate(Affiliate $affiliate): void
    {
        if (isset($this->affiliates[$affiliate->handle])) {
            throw new \RuntimeException('this network already holds ' . $affiliate->handle);
        }
        if ($affiliate->sponsor !== null && !isset($this->affiliates[$affiliate->sponsor])) {
            throw new \RuntimeException('a sponsor must already sit in the network');
        }
        $this->affiliates[$affiliate->handle] = $affiliate;
    }

    public function listOffer(Offer $offer): void
    {
        if (!isset($this->businesses[$offer->business])) {
            throw new \RuntimeException('an offer names a business in this network');
        }
        if (isset($this->offers[$offer->handle])) {
            throw new \RuntimeException('this network already holds ' . $offer->handle);
        }
        $this->offers[$offer->handle] = $offer;
    }

    public function offer(string $handle): Offer
    {
        if (!isset($this->offers[$handle])) {
            throw new \RuntimeException('this network holds no offer called ' . $handle);
        }

        return $this->offers[$handle];
    }

    public function affiliate(string $handle): Affiliate
    {
        if (!isset($this->affiliates[$handle])) {
            throw new \RuntimeException('this network holds no affiliate called ' . $handle);
        }

        return $this->affiliates[$handle];
    }

    /** A sale of one offer by one affiliate, at the price the affiliate pays. */
    public function sell(string $offerHandle, string $affiliateHandle, string $day): Sale
    {
        $offer = $this->offer($offerHandle);
        $affiliate = $this->affiliate($affiliateHandle);
        if (!$offer->isOpenOn($day)) {
            throw new \RuntimeException('the offer closed on ' . $offer->closesOn);
        }
        $sale = new Sale($offerHandle, $affiliateHandle, $offer->affiliatePrice(), $day);
        $this->sales[] = $sale;

        return $sale;
    }

    /** @return list<Sale> */
    public function sales(): array
    {
        return $this->sales;
    }

    /**
     * What one affiliate earned on the sales the network holds: the share at their
     * own depth, plus the share one tier below for each affiliate they sponsored.
     */
    public function earned(string $affiliateHandle): int
    {
        $this->affiliate($affiliateHandle);
        $total = 0;
        foreach ($this->sales as $sale) {
            $offer = $this->offer($sale->offer);
            $seller = $this->affiliate($sale->affiliate);
            $mine = $seller->handle === $affiliateHandle ? $offer->share($seller->depth) : 0;
            $brought = $seller->sponsor === $affiliateHandle ? $offer->share($seller->depth - 1) : 0;
            $total += intdiv($sale->amount * ($mine + $brought), 10000);
        }

        return $total;
    }
}
