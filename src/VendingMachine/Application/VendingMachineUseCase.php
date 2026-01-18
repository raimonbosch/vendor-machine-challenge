<?php

namespace VendingMachine\Application;

use VendingMachine\Application\Dto\VendingMachineServiceResponseDTO;
use VendingMachine\Application\Services\VendingMachineParserService;
use VendingMachine\Application\Services\VendingMachineService;
use VendingMachine\Domain\Exceptions\InvalidActionException;
use VendingMachine\Domain\Exceptions\InvalidCoinException;
use VendingMachine\Domain\Exceptions\InvalidVendingMachineInputException;

class VendingMachineUseCase
{
    public function __construct(
        private readonly VendingMachineParserService $vendingMachineParserService,
        private readonly  VendingMachineService $vendingMachineService,
    ) {
    }

    /**
     * @throws InvalidVendingMachineInputException
     * @throws InvalidActionException
     * @throws InvalidCoinException
     */
    public function execute(array $params): string
    {
        if(empty($params)) {
            throw new InvalidVendingMachineInputException("No params given");
        }

        $input = array_values($params);
        if($input[0] === null || !$this->isValidInput($input[0])) {
            throw new InvalidVendingMachineInputException("Input looks incorrect");
        }

        $vendingMachineInput = $this->vendingMachineParserService->execute($input[0]);
        $vendingMachineResponse = $this->vendingMachineService->execute(
            $vendingMachineInput->getAction(),
            $vendingMachineInput->getCoins()
        );

        return $this->processResponse($vendingMachineResponse);
    }

    private function processResponse(VendingMachineServiceResponseDTO $response): string
    {
        $coins = [];
        $output = $response->getMessage();
        foreach ($response->getCoinChange() as $coin) {
            $coins []= $coin->getName();
        }

        if (count($coins) > 0) {
            $output = $output . ', ' . implode(', ', $coins);
        }

        if ($response->getMessage() === 'SERVICE') {
            $output = $output . ', ' . (round($response->getAvailableCents() / 100, 2)) . ', ' . $response->getNumberOfProducts();
        }

        return $output;
    }

    private function isValidInput(string $input): bool
    {
        return preg_match('/^[A-Z0-9.,\s-]*$/', $input) === 1;
    }
}