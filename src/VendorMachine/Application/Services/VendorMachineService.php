<?php

namespace VendorMachine\Application\Services;

use VendorMachine\Application\Dto\VendorMachineServiceResponseDTO;
use VendorMachine\Domain\Exceptions\AllocationCoinsException;
use VendorMachine\Domain\Repository\CoinRepository;
use VendorMachine\Domain\Repository\ProductRepository;
use VendorMachine\Domain\ValueObjects\Action;
use VendorMachine\Domain\ValueObjects\Coin;
use VendorMachine\Domain\ValueObjects\Product;
use VendorMachine\Domain\ValueObjects\Products\Juice;
use VendorMachine\Domain\ValueObjects\Products\Soda;
use VendorMachine\Domain\ValueObjects\Products\Water;

class VendorMachineService
{
    public function __construct(
        private readonly CoinRepository $userCoinsRepository,
        private readonly CoinRepository $cashierRepository,
        private readonly ProductRepository $productRepository) {
    }

    /**
     * @param Action $action
     * @param Coin[] $coins
     * @return VendorMachineServiceResponseDTO
     */
    public function execute(Action $action, array $coins): VendorMachineServiceResponseDTO
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
                return new VendorMachineServiceResponseDTO(
                    null,
                    $this->userCoinsRepository->withdrawChange(),
                    "RETURN-COIN",
                );
            case Action::SERVICE:
                return new VendorMachineServiceResponseDTO(
                    null,
                    [],
                    "SERVICE",
                    $this->productRepository->numberOfProducts(),
                    $this->cashierRepository->availableChange()
                );
        }

        return new VendorMachineServiceResponseDTO(
            null,
            [],
            "Unexpected action"
        );
    }

    private function deliverProduct(Product $product, int $availableCents): VendorMachineServiceResponseDTO
    {
        if ($availableCents < $product->priceInCents()) {
            $this->productRepository->add($product);
            return new VendorMachineServiceResponseDTO(
                null,
                [],
                "Not enough funds"
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

        return new VendorMachineServiceResponseDTO(
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