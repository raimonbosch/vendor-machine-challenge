<?php

namespace VendorMachine\Application;

use VendorMachine\Domain\InvalidVendorMachineInputException;

class VendorMachineUseCase
{
    public function execute(array $params): string
    {
        if(empty($params)) {
            throw new InvalidVendorMachineInputException("No params given");
        }

        $input = array_values($params);
        if($input[0] === null || !$this->isValidInput($input[0])) {
            throw new InvalidVendorMachineInputException("Input looks incorrect");
        }

        return "INSERT COIN";
    }

    private function isValidInput(string $input): bool
    {
        return preg_match('/^[A-Z0-9.,\s-]*$/', $input) === 1;
    }
}