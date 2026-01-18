<?php
declare(strict_types=1);

namespace App\Command\Resource;

use App\Service\Resource\ResourceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:resource:sync',
    description: 'Synchronizes the registered resources to the database'
)]
class AppResourceSyncCommand extends Command
{

    public function __construct(
        private readonly ResourceService $resourceService,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $progressIndicator = new ProgressIndicator($output);

        $output->writeln('Starting resource database synchronization');
        $progressIndicator->start('Processing...');

        $createResult = $this->resourceService->generateResourceTableFromResourceList(
            $this->resourceService->getResourcesFromYaml()
        );

        $processed = $createResult->created + $createResult->exists;

        if($createResult->success === false){
            $progressIndicator->finish('<error>Item synchronisation failed after processing'.$processed.' items!</error>');
            return Command::FAILURE;
        }

        $progressIndicator->finish('Synchronisation completed');
        $output->writeln('Processed '.$processed.' items');
        $output->writeln('Newly created '.$createResult->created.' items');
        $output->writeln('Skipped '.$createResult->exists.' existing items');
        return Command::SUCCESS;
    }

}
