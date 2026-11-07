<?php

declare(strict_types=1);

namespace Asalerator\App;

use Asalerator\Home\Sale;
use Asalerator\Home\Sample;

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

    public function front(Sample $sample): string
    {
        $TITLE = 'Asalerator, one item sold at a time';
        $LEDE = 'Asalerator runs sales where buyers compete by bidding against each other.';
        $WHAT = 'Each sale has a title, opening price, bidding step and closing day. A buyer submits a bid with their handle, amount and arrival day. The bid must meet the opening price and beat the standing bid by at least the required step. Buyers can bid more than once, with each later bid replacing their earlier one. After the closing day, the sale takes no more bids. The winner pays one step above the previous standing bid, not their own bid amount.';

        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">Sales: ' . count($sample->sales()) . '. Bids taken: ' . $sample->bids()
            . '. Closed with a winner: ' . count(array_filter($sample->sales(), static fn (Sale $s): bool => $s->standing() !== null)) . '.</p>' . "\n";

        $out .= '<div class="sales">' . "\n";
        foreach ($sample->sales() as $sale) {
            $out .= $this->card($sale);
        }
        $out .= '</div>' . "\n";

        $out .= '<h2>What a sale does</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['Sale opens', 'The sale publishes its title, opening price, step and closing day.'],
            ['Bid arrives', 'A buyer submits a bid with their handle and amount.'],
            ['Standing bid moves', 'A valid higher bid replaces the previous bid from that buyer.'],
            ['Sale closes', 'The sale stops accepting bids after its closing day.'],
            ['Winner is named', 'The result gives the winner, price and final bidding details.'],
        ] as [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    private function card(Sale $sale): string
    {
        $answer = $sale->answer();
        $out = '<article class="sale">' . "\n";
        $out .= '<h2>' . $this->h($sale->title) . '</h2>' . "\n";
        $out .= '<p class="where">Opens at ' . $this->h($this->money($sale->opening)) . ', step ' . $this->h($this->money($sale->step))
            . ', closes ' . $this->h($sale->closingDay) . '. Bids: ' . $sale->bids() . '.</p>' . "\n";
        if ($answer['buyer'] === null) {
            $out .= '<p class="unsold">Closed unsold. No buyer holds a bid.</p>' . "\n";
        } else {
            $out .= '<p class="sold">' . $this->h((string) $answer['buyer']) . ' wins at ' . $this->h($this->money((int) $answer['price']))
                . '. The standing bid was ' . $this->h($this->money((int) $answer['standing'])) . '.</p>' . "\n";
        }
        $out .= '</article>' . "\n";

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That path does not exist here.</p>\n" . $this->footer();
    }
}
