<?php

namespace VendorMachine\Infrastructure\Repository;

use VendorMachine\Domain\Repository\CoinRepository;
use VendorMachine\Domain\Services\CoinAllocationService;
use VendorMachine\Domain\ValueObjects\Coin;

class CoinArrayRepository implements CoinRepository
{
    /**
     * @var Coin[]
     */
    private array $coins;

    public function __construct(private readonly CoinAllocationService $coinAllocationService) {
    }

    public function add(Coin $coin)
    {
        $this->coins[] = $coin;
    }

    /**
     * @param int $cents
     * @return Coin[]
     */
    public function subtract(int $cents): array
    {
        $allocatedCoins = $this->coinAllocationService->allocateCoins($this->coins, $cents);
        foreach ($allocatedCoins as $allocatedCoin) {
            $this->popAllocatedCoin($allocatedCoin);
        }

        return $allocatedCoins;
    }

    /**
     * @return Coin[]
     */
    public function getChange(): array
    {
        $coinsToDeliver = [];
        foreach ($this->coins as $i => $coin) {
            unset($this->coins[$i]);
            $coinsToDeliver []= $coin;
        }

        return $coinsToDeliver;
    }

    private function popAllocatedCoin(Coin $allocatedCoin): void {
        foreach ($this->coins as $i => $coin) {
            if ($coin->getCents() === $allocatedCoin->getCents()) {
                unset($this->coins[$i]);
                $this->coins = array_values($this->coins);
                return;
            }
        }
    }

    public function getAvailableCoins(): array {
        return $this->coins;
    }

    /**
     * @param Coin[] $coins
     * @return void
     */
    public function recharge(array $coins): void
    {
        foreach ($coins as $coin) {
            $this->add($coin);
        }
    }
}