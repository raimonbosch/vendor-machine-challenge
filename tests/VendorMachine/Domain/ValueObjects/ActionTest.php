<?php

namespace Tests\VendorMachine\Domain\ValueObjects;

use CodeIgniter\Test\CIUnitTestCase;
use VendorMachine\Domain\Exceptions\InvalidActionException;
use VendorMachine\Domain\ValueObjects\Action;

class ActionTest extends CIUnitTestCase
{
    public function testValidAction(): void
    {
        $this->assertInstanceOf(Action::class, new Action('GET-SODA'));
    }

    public function testInvalidAction(): void
    {
        $this->expectException(InvalidActionException::class);

        new Action('Whatever');
    }
}