<?php

namespace VendingMachine\Application\Services;

use VendingMachine\Application\Dto\VendingMachineServiceResponseDTO;
use VendingMachine\Domain\Exceptions\AllocationCoinsException;
use VendingMachine\Domain\Repository\CoinRepository;
use VendingMachine\Domain\Repository\ProductRepository;
use VendingMachine\Domain\ValueObjects\Action;
use VendingMachine\Domain\ValueObjects\Coin;
use VendingMachine\Domain\ValueObjects\Product;
use VendingMachine\Domain\ValueObjects\Products\Juice;
use VendingMachine\Domain\ValueObjects\Products\Soda;
use VendingMachine\Domain\ValueObjects\Products\Water;

class VendingMachineService
{
    public function __construct(
        private readonly CoinRepository $userCoinsRepository,
        private readonly CoinRepository $cashierRepository,
        private readonly ProductRepository $productRepository) {
    }

    /**
     * @param Action $action
     * @param Coin[] $coins
     * @return VendingMachineServiceResponseDTO
     */
    public function execute(Action $action, array $coins): VendingMachineServiceResponseDTO
    {
        $availableCents = $this->depositCoins($coins);
        switch($action->getName()) {
            case Action::GET_SODA:
                $product = $this->productRepository->deliver(Soda::NAME);
                return $this->deliverProduct($product, $availableCents);
            case Action::GET_JUICE:
                $product = $this->productRepository->deliver(Juice::NAME);
                return $this->deliverProduct($product, $availableCents);
            case Action::GET_WATER:
                $product = $this->productRepository->deliver(Water::NAME);
                return $this->deliverProduct($product, $availableCents);
            case Action::RETURN_COIN:
                return new VendingMachineServiceResponseDTO(
                    null,
                    $this->userCoinsRepository->withdrawChange(),
                    "RETURN-COIN",
                );
            case Action::SERVICE:
                return new VendingMachineServiceResponseDTO(
                    null,
                    [],
                    "SERVICE",
                    $this->productRepository->numberOfProducts(),
                    $this->cashierRepository->availableChange()
                );
        }

        return new VendingMachineServiceResponseDTO(
            null,
            [],
            "Unexpected action"
        );
    }

    private function deliverProduct(Product $product, int $availableCents): VendingMachineServiceResponseDTO
    {
        if ($availableCents < $product->priceInCents()) {
            $this->productRepository->add($product);
            return new VendingMachineServiceResponseDTO(
                null,
                [],
                "NOT-ENOUGH-FUNDS"
            );
        }

        try{
            $cashierCoins = $this->userCoinsRepository->subtract($product->priceInCents());
            $changeCoins = $this->userCoinsRepository->withdrawChange();
        } catch (AllocationCoinsException $e) {
            $cashierCoins = $this->userCoinsRepository->withdrawChange();
            $changeCoins = $this->cashierRepository->subtract($availableCents - $product->priceInCents());
        }

        foreach ($cashierCoins as $coin) {
            $this->cashierRepository->add($coin);
        }

        return new VendingMachineServiceResponseDTO(
            $product,
            $changeCoins,
            $product->name()
        );
    }

    /**
     * @param Coin[] $coins
     * @return int
     */
    private function depositCoins(array $coins): int {
        $cents = 0;
        foreach ($coins as $coin) {
            $this->userCoinsRepository->add($coin);
            $cents += $coin->getCents();
        }

        return $cents;
    }
}