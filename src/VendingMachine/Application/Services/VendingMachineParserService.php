<?php

namespace VendingMachine\Application\Services;

use VendingMachine\Application\Dto\VendingMachineParserResponseDTO;
use VendingMachine\Domain\Exceptions\InvalidActionException;
use VendingMachine\Domain\Exceptions\InvalidCoinException;
use VendingMachine\Domain\ValueObjects\Action;
use VendingMachine\Domain\ValueObjects\Coin;

class VendingMachineParserService
{
    /**
     * @throws InvalidCoinException
     * @throws InvalidActionException
     */
    public function execute(string $input): VendingMachineParserResponseDTO {
        $action = null;
        $coins = [];
        $elements = explode(',', $input);
        foreach ($elements as $element) {
            $element = trim($element);
            if ($this->isCoin($element)) {
                $coins []= new Coin($element);
            }

            if ($this->isAction($element)) {
                $action = new Action($element);
            }
        }

        if ($action === null) {
            throw new InvalidActionException();
        }

        return new VendingMachineParserResponseDTO($action, $coins);
    }

    private function isCoin(string $element): bool {
        return Coin::isValid($element);
    }

    private function isAction(string $element): bool {
        return Action::isValid($element);
    }
}