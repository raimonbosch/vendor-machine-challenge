<?php

namespace VendorMachine\Domain\Services;

use VendorMachine\Domain\Exceptions\AllocationCoinsException;
use VendorMachine\Domain\ValueObjects\Coin;

class CoinAllocationService
{
    /**
     * This is an allocation algorithm using a O(n²) performance. Should be enough for a vending machine use case.
     * @param Coin[] $coins
     * @param int $amountInCents
     * @return Coin[]
     * @throws AllocationCoinsException
     */
    public function allocateCoins(array $coins, int $amountInCents): array
    {
        usort($coins, fn($a, $b) => $b->getCents() <=> $a->getCents());

        for ($start = 0; $start < count($coins); $start++) {
            $result = $this->chooseCoins($coins, $amountInCents, $start);
            if (count($result) > 0) {
                return $result;
            }
        }

        throw new AllocationCoinsException();
    }

    /**
     * @param Coin[] $coins
     * @param int $amountInCents
     * @param int $start
     * @return Coin[]
     */
    private function chooseCoins(array $coins, int $amountInCents, int $start = 0): array
    {
        $chosenCoins = [];
        foreach ($coins as $i => $coin) {
            if ($start > $i) {
                continue;
            }
            if ($coin->getCents() <= $amountInCents) {
                $chosenCoins []= $coin;
            }

            if ($this->sumInCents($chosenCoins) === $amountInCents) {
                return $chosenCoins;
            }

            if ($this->sumInCents($chosenCoins) > $amountInCents) {
                array_pop($chosenCoins);
            }
        }

        return [];
    }

    private function sumInCents(array $coins): int {
        $sumInCents = 0;
        foreach ($coins as $coin) {
            $sumInCents += $coin->getCents();
        }

        return $sumInCents;
    }
}