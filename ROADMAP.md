# Asalerator roadmap

Asalerator sells one item to a room of buyers. Version 1 holds the sale, the bids
and the price rule in one process, and the list below is what a second sale and a
second buyer need.

## Open in the model

- The winner pays one step over the bid that stood before. A seller cannot change
  that rule, and a sale where the winner pays their own bid waits for a new draft
  of SPEC.md.
- A reserve price under the opening price is out of scope, so a sale that closes
  under a reserve cannot say so.
- Two bids on the last day at the same amount keep the earlier one. The rule that
  settles it is not written down anywhere but the code.

## Owed to a buyer

- A buyer cannot withdraw a bid.
- A buyer cannot see who else holds a bid. The page shows the standing amount and
  the count.
- Nothing tells a buyer that a bid landed. The caller has to read the answer.

## Owed to a seller

- A sale cannot be withdrawn once it opens.
- A sale cannot be extended. The closing day is fixed at the start.
- There is no way to close a sale early.

## Owed to a reader

- The page shows every sale on the server and holds no filters.
- The sale keeps only the standing bid and the bid before it, so a reader cannot
  follow the whole run of bids.
- No bid carries a seal, so a reader cannot tell a bid this sale took from one
  written in afterwards.

## Checks owed

- Two bids on the closing day at the same amount.
- A sale that closes on the day the first bid arrives.
- A step of one cent and a step larger than the opening price.
