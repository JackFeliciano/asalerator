# Asalerator

Asalerator matches a business that has something to sell with an affiliate network that
already exists. The business lists an offer, an affiliate takes it and moves it through
their own contacts, and the network pays the commission the offer defines at each tier.

## What it holds

A business carries a handle, a name and a sector of industry, commerce or services. An
offer carries a price, an affiliate discount, commission tiers and a closing day. An
affiliate carries a handle, a sponsor and a depth in the network. A sale carries the
offer, the affiliate, the amount paid and the day.

## What the network enforces

A handle is unique inside the network. A sponsor must already sit in the network when an
affiliate joins. An offer names a business the network holds. A sale after the closing day
lands nowhere.

## What an affiliate earns

The tier at their own depth on each sale they make. The tier one level above the seller
on each sale made by an affiliate they sponsored. A sale two levels down pays nothing.

## Routes

    /              the offers, the affiliates and the earnings
    /health        counts and the total paid out
    /assets/site.css

## Running it

    ASALERATOR_BASE=/ php -S 127.0.0.1:8107 -t showroom showroom/index.php
    php tests/run.php

## The sample

Three businesses, four offers, four affiliates across three tiers, three sales and 26640
cents of commission.

## Layout

    src/Home      Business, Offer, Affiliate, Sale, Network, Sample
    src/App       Front, Router, Views
    showroom      index.php and assets
    tests         the runner and the suite

## Licence

Mozilla Public License 2.0. See LICENSE.
