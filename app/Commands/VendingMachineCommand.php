<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\Commands;
use Psr\Log\LoggerInterface;
use VendingMachine\Application\VendingMachineUseCase;
use VendingMachine\Domain\Exceptions\InvalidActionException;
use VendingMachine\Domain\Exceptions\InvalidVendingMachineInputException;

class VendingMachineCommand extends BaseCommand
{
    protected $group = 'VendingMachine';
    protected $name = 'vendor_machine:run';
    protected $description = 'Test command';

    public function __construct(LoggerInterface $logger, Commands $commands)
    {
        parent::__construct($logger, $commands);
    }

    /**
     * @throws InvalidVendingMachineInputException
     */
    public function run(array $params)
    {
        try {
            /** @var VendingMachineUseCase $useCase */
            $useCase = service('VendingMachineUseCase');
            CLI::write($useCase->execute($params));
        } catch (InvalidVendingMachineInputException $e) {
            $this->logger->error($e->getMessage(), ['params' => $params]);
            CLI::write("ERR_INPUT");
        } catch (InvalidActionException $e) {
            $this->logger->error($e->getMessage(), ['params' => $params]);
            CLI::write("ERR_INPUT");
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            CLI::write("ERR_UNKNOWN");
        }
    }
}