<?php
declare(strict_types=1);

namespace Merchant\Command;

use App\Exception\Resource\ResourceDoesNotExistException;
use Merchant\DTO\CreateOfferDTO;
use Merchant\Exception\MerchantOfferCouldNotBeCreatedException;
use Merchant\Model\Merchant;
use Merchant\Service\MerchantOfferService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'merchant:offer:list',
    description: 'List all the merchants offers',
)]
class MerchantOfferListCommand extends Command
{

    public function __construct(
        private readonly MerchantOfferService $merchantOfferService,
    )
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $offers = $this->merchantOfferService->getAllCurrentOffers();
        if(empty($offers)) {
            $output->writeln('<info>There are currently no offers from the merchant.</info>');
            return Command::SUCCESS;
        }

        $output->writeln('<info>There are the following offers from the merchant:</info>');
        foreach ($offers as $offerId => $offer) {
            /* @var Merchant $offer */
            $output->writeln('ID: ' . $offerId . ' - Slug:' . $offer->slug->toString());
            $output->writeln('Resource: ' . $offer->resource);
            $output->writeln('Quantity: ' . $offer->quantity);
            $output->writeln('Price: ' . $offer->price);
            $output->writeln('The offer expires at ' . $offer->expires->format(\DateTime::RFC850));
            $output->writeln('');
        }
        return Command::SUCCESS;
    }

}