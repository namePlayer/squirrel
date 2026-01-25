<?php
declare(strict_types=1);

namespace App\Service\Account;

use App\DTO\Account\CreateAccountDTO;
use App\Exception\Account\AccountCreationFailedException;
use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\AccountUpdateFailedException;
use App\Exception\Account\DuplicateAccountEmailException;
use App\Model\Account;
use App\Table\Account\AccountTable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AccountService
{

    public function __construct(
        private readonly AccountTable $accountTable,
        private readonly PasswordService $passwordService
    )
    {
    }

    public function create(CreateAccountDTO $accountDTO): void
    {
        if($this->getAccountByEmail($accountDTO->email) instanceof Account) {
            throw new DuplicateAccountEmailException();
        }

        $account = new Account();
        do {
            $account->slug = Uuid::uuid4();
        } while($this->getAccountBySlug($account->slug) instanceof Account);

        $account->username = $accountDTO->username;
        $account->email = $accountDTO->email;
        $account->passwordHash = $this->passwordService->hashPassword($accountDTO->password);

        if($this->accountTable->insert($account) === false)
        {
            throw new AccountCreationFailedException();
        }
    }

    public function update(Account $account): void
    {
        if($this->accountTable->update($account) === false)
        {
            throw new AccountUpdateFailedException();
        }
    }

    public function getAccountByEmail(string $email): ?Account
    {
        return $this->accountTable->findByEmail($email);
    }

    public function getAccountBySlug(UuidInterface $slug): ?Account
    {
        return $this->accountTable->findBySlug($slug->toString());
    }

    public function getAccountById(int $id): ?Account
    {
        return $this->accountTable->findById($id);
    }

}
