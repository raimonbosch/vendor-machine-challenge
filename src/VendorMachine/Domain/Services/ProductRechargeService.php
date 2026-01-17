<?php

namespace VendorMachine\Domain\Services;

use VendorMachine\Domain\ValueObjects\Products\Juice;
use VendorMachine\Domain\ValueObjects\Products\Soda;
use VendorMachine\Domain\ValueObjects\Products\Water;

class ProductRechargeService
{
    public function getRechargeProducts(): array
    {
        return array_merge(
            array_fill(0, 15,  new Soda()),
            array_fill(0, 5, new Juice()),
            array_fill(0, 20, new Water()),
        );
    }
}