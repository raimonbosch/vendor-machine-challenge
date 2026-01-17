<?php

namespace VendorMachine\Domain\ValueObjects\Products;

use VendorMachine\Domain\ValueObjects\Product;

class Water implements Product
{
    private const NAME = 'WATER';

    public function price(): float
    {
        return 0.65;
    }

    public function name(): string
    {
        return self::NAME;
    }
}