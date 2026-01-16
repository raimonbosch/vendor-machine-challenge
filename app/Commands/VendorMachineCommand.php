<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\Commands;
use Psr\Log\LoggerInterface;
use VendorMachine\Application\VendorMachineUseCase;
use VendorMachine\Domain\InvalidVendorMachineInputException;

class VendorMachineCommand extends BaseCommand
{
    protected $group = 'VendorMachine';
    protected $name = 'vendor_machine:run';
    protected $description = 'Test command';

    public function __construct(LoggerInterface $logger, Commands $commands)
    {
        parent::__construct($logger, $commands);
    }

    /**
     * @throws InvalidVendorMachineInputException
     */
    public function run(array $params)
    {
        try {
            /** @var VendorMachineUseCase $useCase */
            $useCase = service('VendorMachineUseCase');
            CLI::write($useCase->execute($params));
        } catch (InvalidVendorMachineInputException $e) {
            $this->logger->error($e->getMessage(), ['params' => $params]);
            CLI::write("ERR_INPUT");
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            CLI::write("ERR_UNKNOWN");
        }
    }
}