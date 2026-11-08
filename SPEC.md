# Asalerator

Asalerator matches a business that has something to sell with an affiliate network
that already exists. The business lists an offer, an affiliate takes it and moves it
through their own contacts, and the network pays the commission the offer defines at
each tier.

## Words

- **business**: one seller in industry, commerce or services. It carries a handle, a
  name and a sector.
- **offer**: one product or service a business lists. It carries a price in cents, a
  discount in basis points for the affiliate who buys it for their own use, a list of
  commission tiers in basis points and a closing day.
- **affiliate**: one person in the network. It carries a handle, a name, a sponsor and
  a depth. An affiliate at depth one names no sponsor.
- **tier**: one level of the network, counted from the business. Tier one holds the
  affiliate the business talks to.
- **sale**: one offer moved by one affiliate on one day, at the price the affiliate
  pays.

## What the network enforces

- A handle is unique inside a network, for businesses, affiliates and offers alike.
- A sponsor must already sit in the network when a new affiliate joins.
- An offer names a business the network holds, and it carries at least one tier.
- The discount sits below the whole price, and each tier sits between 1 and 10000
  basis points.
- A sale after the closing day lands nowhere.
- A sale names an offer and an affiliate the network holds.

## What the affiliate earns

An affiliate earns the tier at their own depth on each sale they make. An affiliate
also earns the tier one level above the seller on each sale made by an affiliate they
sponsored. A sale by an affiliate two levels down pays the affiliate nothing.

The answer comes back in whole cents, computed as the tier in basis points applied to
the price the affiliate paid.

## Routes

- `/` the offers, the affiliates and the earnings of the sample network
- `/health` JSON, the count of offers, affiliates and sales with the total paid out
- `/assets/site.css` the stylesheet

## Running it

    ASALERATOR_BASE=/ php -S 127.0.0.1:8107 -t showroom showroom/index.php

The suite runs with plain PHP and no dependencies:

    php tests/run.php

## The sample

Three businesses list four offers across three tiers. Ana Reis sits at tier one.
Bright Faria joined under Ana at tier two. Kestrel Dias joined under Bright at tier
three. Marchmont Costa sits at tier one on their own. The sample holds three sales and
pays 26640 cents in commission.

## Layout

    src/Home      Business, Offer, Affiliate, Sale, Network, Sample
    src/App       Front, Router, Views
    showroom      index.php and assets
    tests         the runner and the suite

## Licence

Mozilla Public License 2.0. See LICENSE.
