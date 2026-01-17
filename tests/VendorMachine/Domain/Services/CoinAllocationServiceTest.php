<?php

namespace Tests\VendorMachine\Domain\Services;

use CodeIgniter\Test\CIUnitTestCase;
use VendorMachine\Domain\Services\CoinAllocationService;
use VendorMachine\Domain\ValueObjects\Coin;

class CoinAllocationServiceTest extends CIUnitTestCase
{
    private CoinAllocationService $sut;

    protected function setUp(): void
    {
        $this->sut = new CoinAllocationService();
    }
    /**
     * @dataProvider allocationProvider
     */
    public function testAllocations(array $availableCoins, int $amount, array $expected): void
    {
        $result = $this->sut->allocateCoins($availableCoins, $amount);

        $this->assertEquals($expected, $result);
    }

    public static function allocationProvider(): array
    {
        return [
            'exact 1 euro' => [
                [Coin::euro()],
                100,
                [Coin::euro()],
            ],

            'two quarters' => [
                [Coin::quarter(), Coin::quarter()],
                50,
                [Coin::quarter(), Coin::quarter()],
            ],

            'mixed coins' => [
                [Coin::euro(), Coin::quarter(), Coin::tenCents(), Coin::fiveCents()],
                40,
                [Coin::quarter(), Coin::tenCents(), Coin::fiveCents()],
            ],

            'insufficient amount returns empty' => [
                [Coin::tenCents()],
                50,
                [],
            ],

            'zero amount returns empty' => [
                [Coin::euro(), Coin::quarter()],
                0,
                [],
            ],
        ];
    }
}