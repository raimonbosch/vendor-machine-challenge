<?php

namespace Tests\VendingMachine\Application\Services;

use CodeIgniter\Test\CIUnitTestCase;
use VendingMachine\Application\Services\VendingMachineParserService;
use VendingMachine\Domain\Exceptions\InvalidActionException;
use VendingMachine\Domain\ValueObjects\Action;
use VendingMachine\Domain\ValueObjects\Coin;

class VendingMachineParserServiceTest extends CIUnitTestCase
{
    private VendingMachineParserService $sut;

    protected function setUp(): void
    {
        $this->sut = new VendingMachineParserService();
    }

    public function testValidAction() {
        $response = $this->sut->execute('1, GET-SODA');
        $this->assertEquals(new Action(Action::GET_SODA), $response->getAction());
        $this->assertEquals([Coin::euro()], $response->getCoins());
    }

    public function testInvalidAction() {
        $this->expectException(InvalidActionException::class);
        $this->sut->execute('1, SODA');
    }
}

