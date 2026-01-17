<?php

namespace VendorMachine\Domain\ValueObjects\Products;

use VendorMachine\Domain\ValueObjects\Product;

class Juice implements Product
{
    public const NAME = 'JUICE';

    public function priceInCents(): int
    {
        return 100;
    }

    public function name(): string
    {
        return self::NAME;
    }
}