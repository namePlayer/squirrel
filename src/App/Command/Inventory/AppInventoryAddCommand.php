<?php
declare(strict_types=1);

namespace App\Command\Inventory;

use App\Service\Resource\InventoryService;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:inventory:add',
    description: 'Add a item to a users inventory'
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
        $this->setHidden(true);

        $this->addArgument('accountId', InputArgument::REQUIRED);
        $this->addArgument('itemUid', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accountId = Uuid::fromString($input->getArgument('accountId'));

        var_dump($this->inventoryService->getAccountInventory($accountId));
        return Command::SUCCESS;
    }

}
