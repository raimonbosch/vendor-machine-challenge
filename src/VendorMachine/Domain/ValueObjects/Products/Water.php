<?php

namespace VendorMachine\Domain\ValueObjects\Products;

use VendorMachine\Domain\ValueObjects\Product;

class Water implements Product
{
    public const NAME = 'WATER';

    public function priceInCents(): int
    {
        return 65;
    }

    public function name(): string
    {
        return self::NAME;
    }
}