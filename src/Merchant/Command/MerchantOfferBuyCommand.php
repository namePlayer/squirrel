<?php
declare(strict_types=1);

namespace Merchant\Command;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\MoneyCanNotBeLessThanZeroException;
use App\Exception\Account\MoneyCouldNotBeWithdrawnFromAccountException;
use App\Exception\Inventory\ResourceCouldNotBeAddedToInventoryException;
use App\Exception\Resource\ResourceDoesNotExistException;
use Merchant\DTO\BuyOfferDTO;
use Merchant\Exception\MerchantInvalidOfferException;
use Merchant\Exception\MerchantOfferBuyQuantityCanNotBeZeroOrLessException;
use Merchant\Exception\MerchantOfferCouldNotBeFoundException;
use Merchant\Service\MerchantTransactionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'merchant:offer:buy',
    description: 'Buy a merchant offer',
)]
class MerchantOfferBuyCommand extends Command
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
        $this->addArgument('offerId', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accountId = $input->getArgument('accountId');
        if(!is_numeric($accountId)) {
            $output->writeln('<error>Invalid accountId</error>');
            return Command::FAILURE;
        }
        $accountId = (int) $accountId;
        $offerId = $input->getArgument('offerId');
        if(!is_numeric($offerId)) {
            $output->writeln('<error>Invalid offerId</error>');
            return Command::FAILURE;
        }
        $offerId = (int) $offerId;
        $amount = $input->getArgument('amount') ?? 0;
        if(!is_numeric($amount)) {
            $output->writeln('<error>Invalid amount</error>');
            return Command::FAILURE;
        }
        $amount = (int) $amount;

        $buyOfferDto = new BuyOfferDTO(
            $offerId, $accountId, $amount
        );

        try {
            $this->merchantTransactionService->buyItemFromOffer($buyOfferDto);
            return Command::SUCCESS;
        } catch (AccountNotFoundException $e) {
            $output->writeln('<error>Account not found</error>');
        } catch (MoneyCanNotBeLessThanZeroException $e) {
            $output->writeln('<error>Account money is not sufficient</error>');
        } catch (MoneyCouldNotBeWithdrawnFromAccountException $e) {
            $output->writeln('<error>Money could not be withdrawn from Account</error>');
        } catch (ResourceCouldNotBeAddedToInventoryException $e) {
            $output->writeln('<error>Resource could not be added to inventory</error>');
        } catch (ResourceDoesNotExistException $e) {
            $output->writeln('<error>Resource does not exist</error>');
        } catch (MerchantInvalidOfferException $e) {
            $output->writeln('<error>Merchant invalid offer</error>');
        } catch (MerchantOfferBuyQuantityCanNotBeZeroOrLessException $e) {
            $output->writeln('<error>Buy offer cannot be zero or less</error>');
        } catch (MerchantOfferCouldNotBeFoundException $e) {
            $output->writeln('<error>Merchant offer could not be found</error>');
        }
        return Command::FAILURE;
    }

}
