<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use VendorMachine\Application\Services\VendorMachineParserService;
use VendorMachine\Application\Services\VendorMachineService;
use VendorMachine\Application\VendorMachineUseCase;
use VendorMachine\Domain\Services\CoinAllocationService;
use VendorMachine\Domain\Services\CoinRechargeService;
use VendorMachine\Domain\Services\ProductRechargeService;
use VendorMachine\Infrastructure\Repository\CoinArrayRepository;
use VendorMachine\Infrastructure\Repository\ProductArrayRepository;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    public static function vendorMachineUseCase(): VendorMachineUseCase
    {
        $coinRechargeService = new CoinRechargeService();
        $productRechargeService = new ProductRechargeService();
        $cashierRepository = new CoinArrayRepository(new CoinAllocationService());
        $cashierRepository->recharge($coinRechargeService->getRechargeCoins());
        $productRepository = new ProductArrayRepository();
        $productRepository->recharge($productRechargeService->getRechargeProducts());

        return new VendorMachineUseCase(
            new VendorMachineParserService(),
            new VendorMachineService(
                new CoinArrayRepository(new CoinAllocationService()),
                $cashierRepository,
                $productRepository
            )
        );
    }
}
