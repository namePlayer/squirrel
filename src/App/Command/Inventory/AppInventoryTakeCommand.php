<?php
declare(strict_types=1);

namespace App\Command\Inventory;

use App\Exception\Inventory\AccountResourceAmountCantBeLessThanZeroException;
use App\Exception\Inventory\AccountResourceIsNotInInventoryException;
use App\Exception\Inventory\ResourceCouldNotBeAddedToInventoryException;
use App\Exception\Inventory\ResourceCouldNotBeTakenFromInventoryException;
use App\Model\Account;
use App\Model\Resource;
use App\Service\Account\AccountService;
use App\Service\Resource\InventoryService;
use App\Service\Resource\ResourceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:inventory:take',
    description: 'Take a item from an users inventory'
)]
class AppInventoryTakeCommand extends Command
{

    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly AccountService $accountService,
        private readonly ResourceService $resourceService,
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
        if(!is_numeric($accountId)) {
            $output->writeln('<error>Account ID is not valid.</error>');
            return Command::FAILURE;
        }
        $accountId = (int)$accountId;
        if(!$this->accountService->getAccountById($accountId) instanceof Account) {
            $output->writeln('<error>Account with ID '. $accountId .' not found.</error>');
            return Command::FAILURE;
        }

        $resourceUid = $input->getArgument('resourceUid');
        if(!$this->resourceService->getResourceByUid($resourceUid) instanceof Resource) {
            $output->writeln('<error>Resource '.$resourceUid.' does not exist.</error>');
            return Command::FAILURE;
        }

        $amount = $input->getArgument('amount');
        if(!is_numeric($amount) || $amount < 1) {
            $output->writeln('<error>Amount is not valid. It has to be numeric and greater than 0.</error>');
            return Command::FAILURE;
        }

        try {
            $this->inventoryService->takeFromInventory($accountId, $resourceUid, (int)$amount);
            $output->writeln('<info>Item taken.</info>');
            return Command::SUCCESS;
        } catch (AccountResourceAmountCantBeLessThanZeroException $e) {
            $output->writeln(
                '<error>Item could not be taken from the users inventory due to the new amount being less than zero.</error>');
            return Command::FAILURE;
        } catch (AccountResourceIsNotInInventoryException $e) {
            $output->writeln('<error>Item could not be taken from the users inventory as its not in there.</error>');
            return Command::FAILURE;
        } catch (ResourceCouldNotBeTakenFromInventoryException $e) {
            $output->writeln('<error>Item could not be taken from the users inventory due to an unknown error.</error>');
            return Command::FAILURE;
        }
    }

}
