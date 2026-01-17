<?php

namespace VendorMachine\Infrastructure\Repository;

use VendorMachine\Domain\Repository\ProductRepository;
use VendorMachine\Domain\ValueObjects\Product;

class ProductArrayRepository implements ProductRepository
{
    private array $repositoryProducts = [];

    public function add(Product $product): void
    {
        $this->repositoryProducts[] = $product;
    }

    public function deliver(Product $product): ?Product
    {
        $productToDeliver = null;
        foreach ($this->repositoryProducts as $i => $repositoryProduct) {
            if ($repositoryProduct->name() === $product->name()) {
                $productToDeliver = $repositoryProduct;
                unset($this->repositoryProducts[$i]);
            }
        }

        $this->repositoryProducts = array_values($this->repositoryProducts);

        return $productToDeliver;
    }

    public function getAvailableProducts(): array {
        return $this->repositoryProducts;
    }
}