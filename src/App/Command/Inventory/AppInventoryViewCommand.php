<?php
declare(strict_types=1);

namespace App\Command\Inventory;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Resource\ResourceDoesNotExistException;
use App\Model\Account;
use App\Service\Account\AccountService;
use App\Service\Economy\InventoryService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:inventory:view',
    description: 'Add a item to a users inventory'
)]
class AppInventoryViewCommand extends Command
{

    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly AccountService $accountService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('accountId', InputArgument::REQUIRED);
        $this->addOption('itemUid', null, InputOption::VALUE_REQUIRED);
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

        $resourceUid = $input->getOption('itemUid');
        try {
            if(!empty($resourceUid)) {
                $resource = $this->inventoryService->getAccountInventoryItemAmount($accountId, $resourceUid);
                if($resource !== null) {
                    $inventory[] = $resource;
                }
            }

            if(empty($inventory)) {
                $inventory = $this->inventoryService->getAccountInventory($accountId);
            }

            foreach ($inventory as $inventoryItem) {
                $output->writeln($inventoryItem->resource);
                $output->writeln('=========');
                $output->writeln('Amount: ' . $inventoryItem->quantity);
                $output->writeln('');
            }

            return Command::SUCCESS;
        } catch (AccountNotFoundException $e) {
            $output->writeln('<error>Account with ID '.$accountId.' not found.</error>');
        } catch (ResourceDoesNotExistException $e) {
            $output->writeln('<error>Resource '.$resourceUid.' does not exist.</error>');
        }
        return Command::FAILURE;
    }

}
