<?php

namespace Tests\VendorMachine\Infrastructure\Repository;

use CodeIgniter\Test\CIUnitTestCase;
use VendorMachine\Domain\Services\CoinAllocationService;
use VendorMachine\Domain\ValueObjects\Coin;
use VendorMachine\Infrastructure\Repository\CoinArrayRepository;

class CoinArrayRepositoryTest extends CIUnitTestCase
{
    private CoinArrayRepository $sut;

    protected function setUp(): void
    {
        $this->sut = new CoinArrayRepository(
            new CoinAllocationService()
        );
    }

    public function testAddAndSubtract(): void
    {
        $this->sut->add(Coin::euro());
        $this->sut->add(Coin::quarter());
        $this->sut->add(Coin::quarter());

        $coins = $this->sut->subtract(125);

        $this->assertEquals([Coin::euro(), Coin::quarter()], $coins);
        $this->assertEquals([Coin::quarter()], $this->sut->getAvailableCoins());
    }
}
