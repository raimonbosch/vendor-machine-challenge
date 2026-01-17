<?php

namespace Tests\VendorMachine\Application\Services;

use CodeIgniter\Test\CIUnitTestCase;
use VendorMachine\Application\Services\VendorMachineService;
use VendorMachine\Domain\Services\CoinAllocationService;
use VendorMachine\Domain\ValueObjects\Action;
use VendorMachine\Domain\ValueObjects\Coin;
use VendorMachine\Domain\ValueObjects\Products\Juice;
use VendorMachine\Domain\ValueObjects\Products\Soda;
use VendorMachine\Domain\ValueObjects\Products\Water;
use VendorMachine\Infrastructure\Repository\CoinArrayRepository;
use VendorMachine\Infrastructure\Repository\ProductArrayRepository;

class VendorMachineServiceTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        $coinAllocationService = new CoinAllocationService();
        $coinCashierRepository = new CoinArrayRepository($coinAllocationService);
        for ($i = 0; $i < 5; $i++) {
            $coinCashierRepository->add(Coin::euro());
        }
        for ($i = 0; $i < 20; $i++) {
            $coinCashierRepository->add(Coin::quarter());
        }
        for ($i = 0; $i < 10; $i++) {
            $coinCashierRepository->add(Coin::tenCents());
        }
        for ($i = 0; $i < 50; $i++) {
            $coinCashierRepository->add(Coin::fiveCents());
        }

        $productRepository = new ProductArrayRepository();
        $productRepository->add(new Soda());
        $productRepository->add(new Soda());
        $productRepository->add(new Water());
        $productRepository->add(new Water());
        $productRepository->add(new Juice());
        $productRepository->add(new Juice());

        $this->sut = new VendorMachineService(
            new CoinArrayRepository($coinAllocationService),
            $coinCashierRepository,
            $productRepository
        );
    }

    public function testBuySoda() {
        $result = $this->sut->execute(new Action(Action::GET_SODA), [Coin::euro(), Coin::quarter(), Coin::quarter()]);

        $this->assertEquals(new Soda(), $result->product);
        $this->assertEquals([], $result->coinChange);
        $this->assertEquals(Soda::NAME, $result->message);
    }

    public function testBuySodaWithChange() {
        $result = $this->sut->execute(new Action(Action::GET_SODA), [Coin::euro(), Coin::quarter(), Coin::quarter(), Coin::tenCents()]);

        $this->assertEquals(new Soda(), $result->product);
        $this->assertEquals([Coin::tenCents()], $result->coinChange);
        $this->assertEquals(Soda::NAME, $result->message);
    }

    public function testBuySodaWithNotEnoughFunds() {
        $result = $this->sut->execute(new Action(Action::GET_SODA), [Coin::euro(), Coin::quarter()]);

        $this->assertEquals(null, $result->product);
        $this->assertEquals([], $result->coinChange);
        $this->assertEquals('Not enough funds', $result->message);
    }

    public function testBuyWaterWithUnevenChange() {
        $result = $this->sut->execute(new Action(Action::GET_WATER), [Coin::quarter(), Coin::quarter(), Coin::quarter()]);

        $this->assertEquals(new Water(), $result->product);
        $this->assertEquals([Coin::tenCents()], $result->coinChange);
        $this->assertEquals(Water::NAME, $result->message);
    }
}