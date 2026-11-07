# Asalerator

## What the project is

Asalerator is a PHP 8.3 program for running sales where buyers compete by bidding against each other. Each sale has a handle, title, opening price in cents, bidding step in cents and closing day.

A buyer submits a bid with their handle, amount in cents and arrival day. A bid below the opening price is rejected. Each new bid must beat the standing bid by at least the required step. A bid that matches the standing bid does not qualify.

Buyers can bid more than once. A later valid bid replaces their earlier bid. The sale refuses bids after its closing day.

When a sale closes, the winner pays one step above the bid that stood before their winning bid. The winner never pays their own bid ceiling. A sale with no valid bids closes unsold.

The result names the winner, price, standing bid, number of bids and closing day.

The demo contains 3 sales and 5 bids. Two sales closed with a winner.

The program uses plain PHP 8.3. It requires no Composer and no packages.

## How to run it

Run the test suite from the repository root:

```bash
php tests/run.php
```

Start the demo with PHP's built-in server:

```bash
php -S 127.0.0.1:8107 -t showroom showroom/index.php
```

The application uses `/` as its default URL prefix. If a proxy passes a different prefix, set `ASALERATOR_BASE` to that value.

## What the routes answer

The demo exposes 2 routes.

`/` renders the sample sales and their results.

`/health` answers with the sales count, bid count and number of sales that closed with a winner.

The home route displays the 3 sample sales and their bidding outcomes. Visitors can see which sales found a winner and which closed unsold.

The health route provides counts from the demonstration data.

## How the layout is arranged

The repository separates sale and bid logic from request handling, presentation, tests and deployment files.

`src/Home/` holds `Bid`, `Sale` and `Sample`.

`src/App/` holds the router, front controller and HTML.

`showroom/` holds the entry file and stylesheet.

`tests/` holds the test suite.

`deploy/` holds the systemd unit and nginx location block.

`SPEC.md` describes the sale rules.

The specification states the project vocabulary, what a sale refuses and what version 1 leaves open.

The src/Home/ directory contains the main sale and bid logic. The src/App/ directory handles requests and page output. The remaining directories support the demo, testing and deployment.

## The state of the suite

The repository includes a hand-written test runner with 16 checks.

Run all checks with:

```bash
php tests/run.php
```

The runner needs no package installation and no test framework. It checks the program from the command line.

## Licence

The repository uses the Mozilla Public License 2.0 (MPL 2.0). You may use, modify and distribute the code. You must publish changes to files covered by the licence.

