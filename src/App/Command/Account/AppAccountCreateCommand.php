<?php
declare(strict_types=1);

namespace App\Command\Account;

use App\DTO\Account\CreateAccountDTO;
use App\Exception\Account\AccountCreationFailedException;
use App\Exception\Account\DuplicateAccountEmailException;
use App\Exception\Account\EmailTooLongException;
use App\Exception\Account\InvalidEmailException;
use App\Exception\Account\UsernameTooLongException;
use App\Exception\Account\UsernameTooShortException;
use App\Service\Account\AccountService;
use App\Service\Account\PasswordService;
use App\Validator\AccountRegistrationValidator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:account:create',
    description: 'Create a new user account'
)]
class AppAccountCreateCommand extends Command
{

    public function __construct(
        private readonly AccountService $accountService,
        private readonly PasswordService $passwordService,
        private readonly AccountRegistrationValidator $accountRegistrationValidator,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $questionHelper = new QuestionHelper();

        $emailQuestion = new Question('Email: ', '');
        $usernameQuestion = new Question('Username: ', '');
        $passwordQuestion = new Question('Password: ', '');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);

        $accountDto = new CreateAccountDTO(
            $questionHelper->ask($input, $output, $emailQuestion),
            $questionHelper->ask($input, $output, $usernameQuestion),
            $questionHelper->ask($input, $output, $passwordQuestion)
        );

        try {
            $this->accountRegistrationValidator->validate($accountDto);
            $this->accountService->create($accountDto);
            return Command::SUCCESS;
        } catch (EmailTooLongException $e) {
            $output->writeln('<error>Email address is too long</error>');
            return Command::FAILURE;
        } catch (InvalidEmailException $e) {
            $output->writeln('<error>Invalid email address</error>');
            return Command::FAILURE;
        } catch (UsernameTooLongException $e) {
            $output->writeln('<error>Username is too long</error>');
            return Command::FAILURE;
        } catch (UsernameTooShortException $e) {
            $output->writeln('<error>Username is too short</error>');
            return Command::FAILURE;
        } catch (AccountCreationFailedException $e) {
            $output->writeln('<error>Account creation failed</error>');
            return Command::FAILURE;
        } catch (DuplicateAccountEmailException $e) {
            $output->writeln('<error>Duplicate account email</error>');
            return Command::FAILURE;
        }
    }

}
