<?php
declare(strict_types=1);

namespace Merchant\Command;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\MoneyCouldNotBeDepositedToAccountException;
use App\Exception\Inventory\AccountResourceAmountCantBeLessThanZeroException;
use App\Exception\Inventory\AccountResourceIsNotInInventoryException;
use App\Exception\Inventory\ResourceCouldNotBeTakenFromInventoryException;
use App\Exception\Resource\ResourceDoesNotExistException;
use Merchant\Service\MerchantTransactionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'merchant:sell',
    description: 'Sell an item out of a users inventory',
)]
class MerchantSellCommand extends Command
{

    public function __construct(
        private readonly MerchantTransactionService $merchantTransactionService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('accountId', InputArgument::REQUIRED);
        $this->addArgument('resourceUid', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::OPTIONAL, '', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accountId = (int)$input->getArgument('accountId');
        $resourceUid = $input->getArgument('resourceUid');
        $quantity = (int)$input->getArgument('amount');

        try {
            $this->merchantTransactionService->sellFromInventory($accountId, $resourceUid, $quantity);
            $output->writeln('<info>'.$quantity.' '. $resourceUid .' have been sold.</info>');
            return Command::SUCCESS;
        } catch (AccountNotFoundException $e) {
            $output->writeln('<error>The searched Account ID could not be found.</error>');
        } catch (MoneyCouldNotBeDepositedToAccountException $e) {
            $output->writeln('<error>The earned money could not be added to the account.</error>');
        } catch (AccountResourceAmountCantBeLessThanZeroException $e) {
            $output->writeln('<error>The new amount of items in the inventory would be less than zero.</error>');
        } catch (AccountResourceIsNotInInventoryException $e) {
            $output->writeln('<error>The searched resource is not in the accounts inventory.</error>');
        } catch (ResourceCouldNotBeTakenFromInventoryException $e) {
            $output->writeln('<error>The wanted item could not be removed out of the players inventory.</error>');
        } catch (ResourceDoesNotExistException $e) {
            $output->writeln('<error>The searched resource does not exist.</error>');
        }
        return Command::FAILURE;
    }

}