<?php

namespace Tests\VendingMachine\Application\Services;

use CodeIgniter\Test\CIUnitTestCase;
use VendingMachine\Application\Services\VendingMachineService;
use VendingMachine\Domain\Services\CoinAllocationService;
use VendingMachine\Domain\Services\CoinRechargeService;
use VendingMachine\Domain\Services\ProductRechargeService;
use VendingMachine\Domain\ValueObjects\Action;
use VendingMachine\Domain\ValueObjects\Coin;
use VendingMachine\Domain\ValueObjects\Products\Juice;
use VendingMachine\Domain\ValueObjects\Products\Soda;
use VendingMachine\Domain\ValueObjects\Products\Water;
use VendingMachine\Infrastructure\Repository\CoinArrayRepository;
use VendingMachine\Infrastructure\Repository\ProductArrayRepository;

class VendingMachineServiceTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        $coinRechargeService = new CoinRechargeService();
        $productRechargeService = new ProductRechargeService();
        $cashierRepository = new CoinArrayRepository(new CoinAllocationService());
        $cashierRepository->recharge($coinRechargeService->getRechargeCoins());
        $productRepository = new ProductArrayRepository();
        $productRepository->recharge($productRechargeService->getRechargeProducts());

        $this->sut = new VendingMachineService(
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

    public function testBuy2Sodas() {
        $resultService1 = $this->sut->execute(new Action(Action::SERVICE), []);

        $result1 = $this->sut->execute(new Action(Action::GET_SODA), [Coin::euro(), Coin::quarter(), Coin::quarter()]);
        $this->assertEquals(new Soda(), $result1->getProduct());
        $this->assertEquals([], $result1->getCoinChange());
        $this->assertEquals(Soda::NAME, $result1->getMessage());

        $result2 = $this->sut->execute(new Action(Action::GET_SODA), [Coin::euro(), Coin::quarter(), Coin::quarter()]);
        $this->assertEquals(new Soda(), $result2->getProduct());
        $this->assertEquals([], $result2->getCoinChange());
        $this->assertEquals(Soda::NAME, $result2->getMessage());

        $resultService2 = $this->sut->execute(new Action(Action::SERVICE), []);

        $this->assertEquals($resultService1->getAvailableCents() + 300, $resultService2->getAvailableCents());
        $this->assertEquals($resultService1->getNumberOfProducts() - 2, $resultService2->getNumberOfProducts());
    }

    public function testBuyJuice() {
        $result = $this->sut->execute(new Action(Action::GET_JUICE), [Coin::euro()]);

        $this->assertEquals(new Juice(), $result->getProduct());
        $this->assertEquals([], $result->getCoinChange());
        $this->assertEquals(Juice::NAME, $result->getMessage());
    }

    public function testBuyWater() {
        $result = $this->sut->execute(new Action(Action::GET_WATER), [Coin::quarter(), Coin::quarter(), Coin::tenCents(), Coin::fiveCents()]);

        $this->assertEquals(new Water(), $result->getProduct());
        $this->assertEquals([], $result->getCoinChange());
        $this->assertEquals(Water::NAME, $result->getMessage());
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
        $this->assertEquals('NOT-ENOUGH-FUNDS', $result->getMessage());
    }

    public function testBuyWaterWithUnevenChange() {
        $result = $this->sut->execute(new Action(Action::GET_WATER), [Coin::quarter(), Coin::quarter(), Coin::quarter()]);

        $this->assertEquals(new Water(), $result->getProduct());
        $this->assertEquals([Coin::tenCents()], $result->getCoinChange());
        $this->assertEquals(Water::NAME, $result->getMessage());
    }

    public function testReturnCoin() {
        $result = $this->sut->execute(new Action(Action::RETURN_COIN), [Coin::euro(), Coin::quarter()]);

        $this->assertEquals(null, $result->getProduct());
        $this->assertEquals([Coin::euro(), Coin::quarter()], $result->getCoinChange());
        $this->assertEquals('RETURN-COIN', $result->getMessage());
    }

    public function testServiceAction() {
        $result = $this->sut->execute(new Action(Action::SERVICE), []);

        $this->assertEquals(40, $result->getNumberOfProducts());
        $this->assertEquals(1350, $result->getAvailableCents());
    }

    public function testUnknownAction() {
        $result = $this->sut->execute(new Action(Action::UNKNOWN), []);
        $this->assertEquals(null, $result->getProduct());
    }
}