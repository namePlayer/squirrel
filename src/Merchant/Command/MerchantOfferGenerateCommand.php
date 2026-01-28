<?php
declare(strict_types=1);

namespace Merchant\Command;

use App\Exception\Resource\ResourceDoesNotExistException;
use Merchant\DTO\CreateOfferDTO;
use Merchant\Exception\MerchantOfferCouldNotBeCreatedException;
use Merchant\Service\MerchantOfferService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'merchant:offer:generate',
    description: 'Create a new selling offer for the merchant',
)]
class MerchantOfferGenerateCommand extends Command
{

    public function __construct(
        private readonly MerchantOfferService $merchantOfferService,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->merchantOfferService->generateMerchantOffers(10);
        return Command::SUCCESS;
    }

}