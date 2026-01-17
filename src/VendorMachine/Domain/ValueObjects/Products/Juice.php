<?php

namespace VendorMachine\Domain\ValueObjects\Products;

use VendorMachine\Domain\ValueObjects\Product;

class Juice implements Product
{
    private const NAME = 'JUICE';

    public function price(): float
    {
        return 1.00;
    }

    public function name(): string
    {
        return self::NAME;
    }
}