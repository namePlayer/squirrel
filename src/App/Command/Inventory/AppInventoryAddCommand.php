<?php
declare(strict_types=1);

namespace App\Command\Inventory;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Inventory\ResourceCouldNotBeAddedToInventoryException;
use App\Exception\Resource\ResourceDoesNotExistException;
use App\Service\Economy\InventoryService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:inventory:add',
    description: 'Add a item to an users inventory'
)]
class AppInventoryAddCommand extends Command
{

    public function __construct(
        private readonly InventoryService $inventoryService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('accountId', InputArgument::REQUIRED);
        $this->addArgument('resourceUid', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accountId = $input->getArgument('accountId');
        $resourceUid = $input->getArgument('resourceUid');
        if(!is_numeric($accountId)) {
            $output->writeln('<error>Account ID is not valid.</error>');
            return Command::FAILURE;
        }
        $accountId = (int)$accountId;

        $amount = $input->getArgument('amount');
        if(!is_numeric($amount) || $amount < 1) {
            $output->writeln('<error>Amount is not valid. It has to be numeric and greater than 0.</error>');
            return Command::FAILURE;
        }
        $amount = (int)$amount;

        try {
            $this->inventoryService->addToInventory($accountId, $resourceUid, $amount);
            $output->writeln('<info>Item added.</info>');
            return Command::SUCCESS;
        } catch (ResourceCouldNotBeAddedToInventoryException $e) {
            $output->writeln('<error>Item could not be added to the users inventory due to an unknown error.</error>');
        } catch (AccountNotFoundException $e) {
            $output->writeln('<error>Account with ID '.$accountId.' could not be found.</error>');
        } catch (ResourceDoesNotExistException $e) {
            $output->writeln('<error>Resource '.$resourceUid.' does not exist.</error>');
        }
        return Command::FAILURE;
    }

}
