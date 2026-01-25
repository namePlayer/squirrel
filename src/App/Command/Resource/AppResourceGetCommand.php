<?php
declare(strict_types=1);

namespace App\Command\Resource;

use App\Exception\Resource\ResourceDoesNotExistException;
use App\Service\Resource\ResourceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:resource:get',
    description: 'Get a resource detail'
)]
class AppResourceGetCommand extends Command
{

    public function __construct(
        private readonly ResourceService $resourceService,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('resourceUid', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $resourceUid = $input->getArgument('resourceUid');
        try {
            $resource = $this->resourceService->getResourceDetailsByUid($resourceUid);
            $output->writeln('<info>Resource UID: '.$resource->uid.'</info>');
            $output->writeln('<info>---------</info>');
            $output->writeln('<info>Price Buy: '.$resource->priceBuy.'</info>');
            $output->writeln('<info>Price Sell: '.$resource->priceSell.'</info>');
            $output->writeln('<info>Always offered by the merchant: '.($resource->merchantAlwaysOffer ? 'true' : 'false').'</info>');
            $output->writeln('<info>Minimum merchant offer: '.$resource->merchantMinOffer.'</info>');
            $output->writeln('<info>Maximum merchant offer: '.$resource->merchantMaxOffer.'</info>');
            return Command::SUCCESS;
        } catch (ResourceDoesNotExistException $e) {
            $output->writeln('<error>Resource '.$resourceUid.' not found.</error>');
        }
        return Command::FAILURE;
    }

}
