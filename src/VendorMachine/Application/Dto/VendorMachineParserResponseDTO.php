<?php

namespace VendorMachine\Application\Dto;

use VendorMachine\Domain\ValueObjects\Action;
use VendorMachine\Domain\ValueObjects\Coin;

class VendorMachineParserResponseDTO
{
    /**
     * @param Action|null $action
     * @param Coin[] $coins
     */
    public function __construct(
        public readonly ?Action $action,
        public readonly array $coins
    ) {
    }
}