<?php

namespace Tests\VendorMachine\Application\Services;

use CodeIgniter\Test\CIUnitTestCase;
use VendorMachine\Application\Dto\VendorMachineServiceResponseDTO;
use VendorMachine\Application\Services\VendorMachineParserService;
use VendorMachine\Application\Services\VendorMachineService;
use VendorMachine\Application\VendorMachineUseCase;
use VendorMachine\Domain\Exceptions\InvalidVendorMachineInputException;
use VendorMachine\Domain\ValueObjects\Coin;
use VendorMachine\Domain\ValueObjects\Products\Soda;

class VendorMachineUseCaseTest extends CIUnitTestCase
{
    private VendorMachineUseCase $sut;
    private VendorMachineService $vendorMachineService;
    protected function setUp(): void
    {
        $this->vendorMachineService = $this->createMock(VendorMachineService::class);
        $this->sut = new VendorMachineUseCase(
            new VendorMachineParserService(),
            $this->vendorMachineService
        );
    }

    public function testBuySoda(): void
    {
        $this->vendorMachineService->method('execute')
            ->willReturn(new VendorMachineServiceResponseDTO(new Soda(), [Coin::quarter()], 'SODA'));

        $result = $this->sut->execute(['1, 0.25, 0.25, 0.25, GET-SODA']);

        $this->assertEquals('SODA, 0.25', $result);
    }

    public function testServiceAction(): void
    {
        $this->vendorMachineService->method('execute')
            ->willReturn(new VendorMachineServiceResponseDTO(null, [], 'SERVICE', 5, 2015));

        $result = $this->sut->execute(['SERVICE']);

        $this->assertEquals('SERVICE, 20.15, 5', $result);
    }

    public function testInvalidInput(): void
    {
        $this->expectException(InvalidVendorMachineInputException::class);
        $this->sut->execute(['@@@@.HELLO']);
    }

    public function testEmptyInput(): void
    {
        $this->expectException(InvalidVendorMachineInputException::class);
        $this->sut->execute([]);
    }
}