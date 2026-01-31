<?php
declare(strict_types=1);

namespace Merchant\Command;

use App\DTO\Resource\ResourceDTO;
use App\Exception\Resource\ResourceDoesNotExistException;
use App\Software;
use Merchant\DTO\CreateOfferDTO;
use Merchant\Exception\MerchantOfferCouldNotBeCreatedException;
use Merchant\Service\MerchantOfferService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Symfony\Component\String\s;

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
        $this->addOption('offerAmount', 'o', InputOption::VALUE_REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $offerAmount = $input->getOption('offerAmount');
        if(empty($offerAmount) || !is_numeric($offerAmount)) {
            $offerAmount = Software::DEFAULT_MERCHANT_OFFER_AMOUNT;
        }
        $offerAmount = (int)$offerAmount;

        $offers = $this->merchantOfferService->generateMerchantOffers($offerAmount);

        $output->writeln('<info>Generated offers:</info>');
        foreach ($offers as $offer) {
            /* @var ResourceDTO $offer */
            $output->writeln('Resource: ' . $offer->uid);
        }

        return Command::SUCCESS;
    }

}