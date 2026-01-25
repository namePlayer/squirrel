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
    name: 'merchant:offer:create',
    description: 'Create a new selling offer for the merchant',
)]
class MerchantOfferCreateCommand extends Command
{

    public function __construct(
        private readonly MerchantOfferService $merchantOfferService,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('resourceUid', InputArgument::REQUIRED);
        $this->addArgument('expires', InputArgument::REQUIRED, 'Relative time to offer expiration');
        $this->addArgument('quantity', InputArgument::OPTIONAL);
        $this->addArgument('price', InputArgument::OPTIONAL);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $quantity = $input->getArgument('quantity');
        if(!empty($quantity) && (!is_numeric($quantity) || $quantity < 1)) {
            $output->writeln(sprintf('<error>Quantity must be numeric and greater than 0.</error>'));
            return Command::FAILURE;
        }

        $price = $input->getArgument('price');
        if(!empty($price) && (!is_numeric($price) || $price < 1)) {
            $output->writeln(sprintf('<error>Price must be numeric and greater than 0.</error>'));
            return Command::FAILURE;
        }

        $createOfferDTO = new CreateOfferDTO(
            $input->getArgument('resourceUid'),
            new \DateTime('now')->modify('+'.$input->getArgument('expires')),
            empty($quantity) ? null : (int) $quantity,
            empty($price) ? null : (int) $price
        );

        try {
            $offer = $this->merchantOfferService->create($createOfferDTO);
            $output->writeln('<info>Merchant offer '.$offer->slug->toString().' (ID: '.$offer->id .') has been created.</info>');
            return Command::SUCCESS;
        } catch (ResourceDoesNotExistException $e) {
            $output->writeln('<error>Resource '.$createOfferDTO->resource.' does not exist.</error>');
        } catch (MerchantOfferCouldNotBeCreatedException $e) {
            $output->writeln('<error>Merchant offer could not be created due to an unknown error.</error>');
        }
        return Command::FAILURE;
    }

}