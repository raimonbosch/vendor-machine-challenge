<?php

namespace VendorMachine\Application\Dto;

use VendorMachine\Domain\ValueObjects\Coin;
use VendorMachine\Domain\ValueObjects\Product;

class VendorMachineServiceResponseDTO
{
    public function __construct(
        private readonly ?Product $product,
        private readonly array $coinChange,
        private readonly string $message,
        private readonly ?int $numberOfProducts = null,
        private readonly ?int $availableCents = null,
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return Coin[]
     */
    public function getCoinChange(): array
    {
        return $this->coinChange ?? [];
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getNumberOfProducts(): ?int
    {
        return $this->numberOfProducts;
    }

    public function getAvailableCents(): ?int
    {
        return $this->availableCents;
    }
}