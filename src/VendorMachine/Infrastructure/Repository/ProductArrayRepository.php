<?php

namespace VendorMachine\Infrastructure\Repository;

use VendorMachine\Domain\Exceptions\ProductNotAvailableException;
use VendorMachine\Domain\Repository\ProductRepository;
use VendorMachine\Domain\ValueObjects\Product;

class ProductArrayRepository implements ProductRepository
{
    private array $repositoryProducts = [];

    public function add(Product $product): void
    {
        $this->repositoryProducts[] = $product;
    }

    public function deliver(string $productName): Product
    {
        $productToDeliver = null;
        foreach ($this->repositoryProducts as $i => $repositoryProduct) {
            if ($repositoryProduct->name() === $productName) {
                $productToDeliver = $repositoryProduct;
                unset($this->repositoryProducts[$i]);
            }
        }

        if ($productToDeliver === null) {
            throw new ProductNotAvailableException("$productName is not available.");
        }

        $this->repositoryProducts = array_values($this->repositoryProducts);

        return $productToDeliver;
    }

    public function getAvailableProducts(): array {
        return $this->repositoryProducts;
    }

    /**
     * @param Product[] $products
     * @return void
     */
    public function recharge(array $products): void
    {
        foreach ($products as $product) {
            $this->add($product);
        }
    }
}