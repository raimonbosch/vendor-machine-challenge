<?php

namespace Tests\VendorMachine\Infrastructure\Repository;

use CodeIgniter\Test\CIUnitTestCase;
use VendorMachine\Domain\ValueObjects\Products\Juice;
use VendorMachine\Domain\ValueObjects\Products\Water;
use VendorMachine\Infrastructure\Repository\ProductArrayRepository;

class ProductArrayRepositoryTest extends CIUnitTestCase
{
    private ProductArrayRepository $sut;

    protected function setUp(): void
    {
        $this->sut = new ProductArrayRepository();
    }

    public function testAddAndDeliver(): void
    {
        $juice = new Juice();

        $this->sut->add($juice);
        $this->assertEquals([$juice], $this->sut->getAvailableProducts());

        $product = $this->sut->deliver($juice);
        $this->assertEquals($juice, $product);
        $this->assertEquals([], $this->sut->getAvailableProducts());
    }

    public function testAddAndNoDeliver(): void
    {
        $juice = new Juice();
        $water = new Water();

        $this->sut->add($juice);

        $product = $this->sut->deliver($water);
        $this->assertNull($product);
        $this->assertEquals([$juice], $this->sut->getAvailableProducts());
    }

}