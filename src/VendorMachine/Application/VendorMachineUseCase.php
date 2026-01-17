<?php

namespace VendorMachine\Application;

use VendorMachine\Application\Dto\VendorMachineServiceResponseDTO;
use VendorMachine\Application\Services\VendorMachineParserService;
use VendorMachine\Application\Services\VendorMachineService;
use VendorMachine\Domain\Exceptions\InvalidActionException;
use VendorMachine\Domain\Exceptions\InvalidCoinException;
use VendorMachine\Domain\Exceptions\InvalidVendorMachineInputException;

class VendorMachineUseCase
{
    public function __construct(
        private readonly VendorMachineParserService $vendorMachineParserService,
        private readonly  VendorMachineService $vendorMachineService,
    ) {
    }

    /**
     * @throws InvalidVendorMachineInputException
     * @throws InvalidActionException
     * @throws InvalidCoinException
     */
    public function execute(array $params): string
    {
        if(empty($params)) {
            throw new InvalidVendorMachineInputException("No params given");
        }

        $input = array_values($params);
        if($input[0] === null || !$this->isValidInput($input[0])) {
            throw new InvalidVendorMachineInputException("Input looks incorrect");
        }

        $vendorMachineInput = $this->vendorMachineParserService->execute($input[0]);
        $vendorMachineResponse = $this->vendorMachineService->execute(
            $vendorMachineInput->action,
            $vendorMachineInput->coins
        );

        return $this->processResponse($vendorMachineResponse);
    }

    private function processResponse(VendorMachineServiceResponseDTO $response): string
    {
        $coins = [];
        $output = $response->getMessage();
        foreach ($response->getCoinChange() as $coin) {
            $coins []= $coin->getName();
        }

        if (count($coins) > 0) {
            $output = $output . ',' . implode(',', $coins);
        }

        return $output;
    }

    private function isValidInput(string $input): bool
    {
        return preg_match('/^[A-Z0-9.,\s-]*$/', $input) === 1;
    }
}