<?php
declare(strict_types=1);

namespace App\Command\Inventory;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Inventory\AccountResourceAmountCantBeLessThanZeroException;
use App\Exception\Inventory\AccountResourceIsNotInInventoryException;
use App\Exception\Inventory\ResourceCouldNotBeTakenFromInventoryException;
use App\Exception\Resource\ResourceDoesNotExistException;
use App\Service\Economy\InventoryService;
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
            $this->inventoryService->takeFromInventory($accountId, $resourceUid, $amount);
            $output->writeln('<info>Item taken.</info>');
            $output->writeln('<info>New Item Amount in Inventory: ' .
                $this->inventoryService->getAccountInventoryItemAmount($accountId, $resourceUid)->quantity.'</info>');
            return Command::SUCCESS;
        } catch (AccountResourceAmountCantBeLessThanZeroException $e) {
            $output->writeln('<error>Item could not be taken from the users inventory due to the new amount being less than zero.</error>');
        } catch (AccountResourceIsNotInInventoryException $e) {
            $output->writeln('<error>Item could not be taken from the users inventory as its not in there.</error>');
        } catch (ResourceCouldNotBeTakenFromInventoryException $e) {
            $output->writeln('<error>Item could not be taken from the users inventory due to an unknown error.</error>');
        } catch (AccountNotFoundException $e) {
            $output->writeln('<error>Account with ID '.$accountId.' not found.</error>');
        } catch (ResourceDoesNotExistException $e) {
            $output->writeln('<error>Item '.$resourceUid.' does not exist.</error>');
        }
        return Command::FAILURE;
    }

}
