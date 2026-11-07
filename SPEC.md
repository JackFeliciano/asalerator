# Asalerator

Asalerator sells one item to a room of buyers who bid against each other. It keeps
the sale, the bids and the rule that closes the sale, and it answers with the price
the winner pays.

## Words

- **sale**: one item offered between two days. It carries a handle, a title, an
  opening price in cents, a step in cents and a closing day.
- **bid**: one offer from one buyer. It carries a buyer handle, an amount in cents
  and the day it arrived.
- **step**: the smallest amount a bid may beat the standing bid by.
- **standing bid**: the highest bid the sale holds.
- **winner**: the buyer holding the standing bid when the sale closes. The winner
  pays one step over the bid that stood before, never the winner's own ceiling.

## What the sale enforces

- A bid below the opening price lands nowhere.
- A bid must beat the standing bid by at least the step. A bid level with the
  standing bid lands nowhere.
- A sale takes no bid after its closing day.
- A buyer may bid more than once, and the later bid replaces the earlier one.
- A sale with no bid closes unsold.

## What the answer holds

The answer names the winner, the price in cents, the standing bid, the number of
bids and the day the sale closed. A sale that never received a bid answers with no
winner and a price of zero.

## Open in version 1

- Whether a buyer may see the standing bid. The demo shows it.
- What happens when two bids arrive on the last day at the same amount. The demo
  keeps the earlier one.
- Whether a reserve price sits under the opening price. The demo has none.
