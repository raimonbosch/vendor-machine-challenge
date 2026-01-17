<?php

namespace VendorMachine\Domain\Services;

use VendorMachine\Domain\ValueObjects\Coin;

class CoinRechargeService
{
    public function getRechargeCoins(): array
    {
        return array_merge(
            array_fill(0, 5,  Coin::euro()),
            array_fill(0, 20, Coin::quarter()),
            array_fill(0, 10, Coin::tenCents()),
            array_fill(0, 50, Coin::fiveCents()),
        );
    }
}