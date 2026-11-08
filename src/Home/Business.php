<?php

declare(strict_types=1);

namespace Asalerator\Home;

/** A business selling one product or service through affiliate networks. */
final class Business
{
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        public readonly string $sector,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a business carries a handle');
        }
        if (trim($name) === '') {
            throw new \RuntimeException('a business carries a name');
        }
        if (!in_array($sector, ['industry', 'commerce', 'services'], true)) {
            throw new \RuntimeException('a business sits in industry, commerce or services');
        }
    }
}
