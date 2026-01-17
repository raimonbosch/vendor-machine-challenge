<?php

namespace VendorMachine\Application\Dto;

use VendorMachine\Domain\ValueObjects\Product;

class VendorMachineServiceResponseDTO
{
    public function __construct(
        public readonly ?Product $product,
        public readonly array $coinChange,
        public readonly string $message
    ) {
    }
}