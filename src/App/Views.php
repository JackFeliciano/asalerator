<?php

declare(strict_types=1);

namespace Asalerator\App;

use Asalerator\Home\Affiliate;
use Asalerator\Home\Network;
use Asalerator\Home\Offer;
use Asalerator\Home\Sale;

/**
 * The HTML. Every string reaching the page goes through h(), and every amount in
 * cents goes through money(), so no bare integer reaches a reader as a price.
 */
final class Views
{
    public function __construct(private readonly string $base = '/')
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', ',') . ' EUR';
    }

    private function share(int $basisPoints): string
    {
        return number_format($basisPoints / 100, 0, '.', ',') . ' percent';
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);
        $t = $this->h($title);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$t}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    public function front(Network $network): string
    {
        $TITLE = 'Asalerator, a marketplace of sales opportunities';
        $LEDE = 'Asalerator is an affiliate sales platform that connects businesses with existing networks of independent sellers.';
        $WHAT = 'A business lists products and services as sales opportunities. An affiliate can buy an opportunity at a discount or refer a buyer for a commission. Each opportunity defines its own commission tiers.';

        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">Offers: ' . count($this->offers($network)) . '. Affiliates: ' . count($this->affiliates($network))
            . '. Sales: ' . count($network->sales()) . '. Paid out: ' . $this->sumPaid($network) . '.</p>' . "\n";

        $out .= '<div class="offers">' . "\n";
        foreach ($this->offers($network) as $offer) {
            $out .= $this->card($network, $offer);
        }
        $out .= '</div>' . "\n";

        $out .= '<h2>Who earns what</h2>' . "\n";
        $out .= '<ul class="earners">' . "\n";
        foreach ($this->affiliates($network) as $affiliate) {
            $out .= '<li>' . $this->earner($network, $affiliate) . '</li>' . "\n";
        }
        $out .= '</ul>' . "\n";

        $out .= '<h2>What an affiliate does</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['List the opportunity', 'A business lists a product or service as a sales opportunity with its own commission tiers.'],
            ['Take the opportunity', 'An affiliate joins the network of independent sellers and takes the opportunity, by buying it at a discount or by referring a buyer.'],
            ['Earn the commission', 'The system records the sale and calculates the commission the affiliate earns.'],
        ] as [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    /** @return list<Offer> */
    private function offers(Network $network): array
    {
        $list = [];
        foreach (['freight-audit', 'freight-pallet', 'alv-survey', 'tab-dinner'] as $handle) {
            $list[] = $network->offer($handle);
        }

        return $list;
    }

    /** @return list<Affiliate> */
    private function affiliates(Network $network): array
    {
        $list = [];
        foreach (['ana', 'bright', 'kestrel', 'marchmont'] as $handle) {
            $list[] = $network->affiliate($handle);
        }

        return $list;
    }

    private function sumPaid(Network $network): string
    {
        $total = 0;
        foreach ($this->affiliates($network) as $affiliate) {
            $total += $network->earned($affiliate->handle);
        }

        return $this->money($total);
    }

    private function earner(Network $network, Affiliate $affiliate): string
    {
        $line = $this->h($affiliate->name) . ' sits at tier ' . $affiliate->depth . '. ';
        $line .= 'Earned ' . $this->h($this->money($network->earned($affiliate->handle))) . ' on this network.';

        return $line;
    }

    private function card(Network $network, Offer $offer): string
    {
        $out = '<article class="offer">' . "\n";
        $out .= '<h2>' . $this->h($offer->title) . '</h2>' . "\n";
        $out .= '<p class="where">Listed at ' . $this->h($this->money($offer->price)) . '. An affiliate pays '
            . $this->h($this->money($offer->affiliatePrice())) . '. Closes ' . $this->h($offer->closesOn) . '.</p>' . "\n";

        $tiers = [];
        foreach ($offer->tiers as $index => $share) {
            $tiers[] = 'tier ' . ($index + 1) . ' ' . $this->share($share);
        }
        $out .= '<p class="terms">Commission: ' . $this->h(implode(', ', $tiers)) . '.</p>' . "\n";

        $moved = 0;
        foreach ($network->sales() as $sale) {
            if ($sale->offer === $offer->handle) {
                $moved++;
            }
        }
        $out .= '<p class="moved">Sales held: ' . $moved . '.</p>' . "\n";
        $out .= '</article>' . "\n";

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That path does not exist here.</p>\n" . $this->footer();
    }
}
