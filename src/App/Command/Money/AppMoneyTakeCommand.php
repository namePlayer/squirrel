<?php

namespace App\Command\Money;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\MoneyCanNotBeLessThanZeroException;
use App\Exception\Account\MoneyCouldNotBeWithdrawnFromAccountException;
use App\Service\Economy\MoneyService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:money:take',
    description: 'Take money from an account',
)]
class AppMoneyTakeCommand extends Command
{

    public function __construct(
        private readonly MoneyService $moneyService,
    )
    {
        parent::__construct();
    }

    public function configure()
    {
        $this->addArgument('accountId', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $accountId = $input->getArgument('accountId');
        if(!is_numeric($accountId))
        {
            $output->writeln('<error>Invalid accountId entered</error>');
            return Command::FAILURE;
        }
        $accountId = (int)$accountId;

        $amount = $input->getArgument('amount');
        if(!is_numeric($amount) || $amount < 1)
        {
            $output->writeln('<error>Invalid amount entered</error>');
            return Command::FAILURE;
        }
        $amount = (int)$amount;

        try {
            $this->moneyService->withdrawMoneyFromAccount($accountId, $amount);
            $output->writeln('<info>Money has been taken from the account.</info>');
            $output->writeln('<info>Account new balance: '.$this->moneyService->getAccountCurrentBalance($accountId).'</info>');
            return Command::SUCCESS;
        } catch (AccountNotFoundException $e) {
            $output->writeln('<error>Account could not be found.</error>');
            return Command::FAILURE;
        } catch (MoneyCanNotBeLessThanZeroException $e) {
            $output->writeln('<error>Money cannot be less than zero.</error>');
            return Command::FAILURE;
        } catch (MoneyCouldNotBeWithdrawnFromAccountException $e) {
            $output->writeln('<error>Money could not be withdrawn from account due to an unknown error.</error>');
            return Command::FAILURE;
        }
    }

}
