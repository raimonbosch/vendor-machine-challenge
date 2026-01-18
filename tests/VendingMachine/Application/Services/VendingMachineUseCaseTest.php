<?php

namespace Tests\VendingMachine\Application\Services;

use CodeIgniter\Test\CIUnitTestCase;
use VendingMachine\Application\Dto\VendingMachineServiceResponseDTO;
use VendingMachine\Application\Services\VendingMachineParserService;
use VendingMachine\Application\Services\VendingMachineService;
use VendingMachine\Application\VendingMachineUseCase;
use VendingMachine\Domain\Exceptions\InvalidVendingMachineInputException;
use VendingMachine\Domain\ValueObjects\Coin;
use VendingMachine\Domain\ValueObjects\Products\Soda;

class VendingMachineUseCaseTest extends CIUnitTestCase
{
    private VendingMachineUseCase $sut;
    private VendingMachineService $vendingMachineService;
    protected function setUp(): void
    {
        $this->vendingMachineService = $this->createMock(VendingMachineService::class);
        $this->sut = new VendingMachineUseCase(
            new VendingMachineParserService(),
            $this->vendingMachineService
        );
    }

    public function testBuySoda(): void
    {
        $this->vendingMachineService->method('execute')
            ->willReturn(new VendingMachineServiceResponseDTO(new Soda(), [Coin::quarter()], 'SODA'));

        $result = $this->sut->execute(['1, 0.25, 0.25, 0.25, GET-SODA']);

        $this->assertEquals('SODA, 0.25', $result);
    }

    public function testBuySodaWithNoEnoughFunds(): void
    {
        $this->vendingMachineService->method('execute')
            ->willReturn(new VendingMachineServiceResponseDTO(null, [], 'NOT-ENOUGH-FUNDS'));

        $result = $this->sut->execute(['1, GET-SODA']);

        $this->assertEquals('NOT-ENOUGH-FUNDS', $result);
    }

    public function testServiceAction(): void
    {
        $this->vendingMachineService->method('execute')
            ->willReturn(new VendingMachineServiceResponseDTO(null, [], 'SERVICE', 5, 2015));

        $result = $this->sut->execute(['SERVICE']);

        $this->assertEquals('SERVICE, 20.15, 5', $result);
    }

    public function testInvalidInput(): void
    {
        $this->expectException(InvalidVendingMachineInputException::class);
        $this->sut->execute(['@@@@.HELLO']);
    }

    public function testEmptyInput(): void
    {
        $this->expectException(InvalidVendingMachineInputException::class);
        $this->sut->execute([]);
    }
}