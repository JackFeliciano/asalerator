<?php

declare(strict_types=1);

namespace Asalerator\Home;

/**
 * One affiliate in one network. The depth says how far the affiliate sits from
 * the business, and the sponsor names the affiliate who brought them in.
 */
final class Affiliate
{
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        public readonly ?string $sponsor,
        public readonly int $depth,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('an affiliate carries a handle');
        }
        if (trim($name) === '') {
            throw new \RuntimeException('an affiliate carries a name');
        }
        if ($depth < 1) {
            throw new \RuntimeException('an affiliate sits at the first tier or below');
        }
        if ($depth === 1 && $sponsor !== null) {
            throw new \RuntimeException('an affiliate at the first tier carries no sponsor');
        }
        if ($depth > 1 && $sponsor === null) {
            throw new \RuntimeException('an affiliate below the first tier names a sponsor');
        }
    }
}
