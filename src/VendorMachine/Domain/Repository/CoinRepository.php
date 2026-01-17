<?php

namespace VendorMachine\Domain\Repository;

use VendorMachine\Domain\ValueObjects\Coin;

interface CoinRepository
{
    public function add(Coin $coin);

    /**
     * @param float $amount
     * @return Coin[]
     */
    public function subtract(int $cents): array;

    /**
     * @return Coin[]
     */
    public function getChange(): array;
}