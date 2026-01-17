<?php

namespace VendorMachine\Domain\ValueObjects\Products;

use VendorMachine\Domain\ValueObjects\Product;

class Soda implements Product
{
    private const NAME = 'SODA';

    public function price(): float
    {
        return 1.50;
    }

    public function name(): string
    {
        return self::NAME;
    }
}