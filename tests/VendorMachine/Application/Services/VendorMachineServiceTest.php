<?php

namespace Tests\VendorMachine\Application\Services;

use CodeIgniter\Test\CIUnitTestCase;
use VendorMachine\Application\Services\VendorMachineService;
use VendorMachine\Domain\Services\CoinAllocationService;
use VendorMachine\Domain\Services\CoinRechargeService;
use VendorMachine\Domain\Services\ProductRechargeService;
use VendorMachine\Domain\ValueObjects\Action;
use VendorMachine\Domain\ValueObjects\Coin;
use VendorMachine\Domain\ValueObjects\Products\Soda;
use VendorMachine\Domain\ValueObjects\Products\Water;
use VendorMachine\Infrastructure\Repository\CoinArrayRepository;
use VendorMachine\Infrastructure\Repository\ProductArrayRepository;

class VendorMachineServiceTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        $coinRechargeService = new CoinRechargeService();
        $productRechargeService = new ProductRechargeService();
        $cashierRepository = new CoinArrayRepository(new CoinAllocationService());
        $cashierRepository->recharge($coinRechargeService->getRechargeCoins());
        $productRepository = new ProductArrayRepository();
        $productRepository->recharge($productRechargeService->getRechargeProducts());

        $this->sut = new VendorMachineService(
            new CoinArrayRepository(new CoinAllocationService()),
            $cashierRepository,
            $productRepository
        );
    }

    public function testBuySoda() {
        $result = $this->sut->execute(new Action(Action::GET_SODA), [Coin::euro(), Coin::quarter(), Coin::quarter()]);

        $this->assertEquals(new Soda(), $result->getProduct());
        $this->assertEquals([], $result->getCoinChange());
        $this->assertEquals(Soda::NAME, $result->getMessage());
    }

    public function testBuySodaWithChange() {
        $result = $this->sut->execute(new Action(Action::GET_SODA), [Coin::euro(), Coin::quarter(), Coin::quarter(), Coin::tenCents()]);

        $this->assertEquals(new Soda(), $result->getProduct());
        $this->assertEquals([Coin::tenCents()], $result->getCoinChange());
        $this->assertEquals(Soda::NAME, $result->getMessage());
    }

    public function testBuySodaWithNotEnoughFunds() {
        $result = $this->sut->execute(new Action(Action::GET_SODA), [Coin::euro(), Coin::quarter()]);

        $this->assertEquals(null, $result->getProduct());
        $this->assertEquals([], $result->getCoinChange());
        $this->assertEquals('Not enough funds', $result->getMessage());
    }

    public function testBuyWaterWithUnevenChange() {
        $result = $this->sut->execute(new Action(Action::GET_WATER), [Coin::quarter(), Coin::quarter(), Coin::quarter()]);

        $this->assertEquals(new Water(), $result->getProduct());
        $this->assertEquals([Coin::tenCents()], $result->getCoinChange());
        $this->assertEquals(Water::NAME, $result->getMessage());
    }
}