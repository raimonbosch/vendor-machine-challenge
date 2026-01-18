<?php

namespace VendingMachine\Application\Dto;

use VendingMachine\Domain\ValueObjects\Action;
use VendingMachine\Domain\ValueObjects\Coin;

class VendingMachineParserResponseDTO
{
    /**
     * @param Action|null $action
     * @param Coin[] $coins
     */
    public function __construct(
        private readonly ?Action $action,
        private readonly array $coins
    ) {
    }

    public function getAction(): ?Action
    {
        return $this->action;
    }

    /**
     * @return Coin[]
     */
    public function getCoins(): array
    {
        return $this->coins;
    }
}