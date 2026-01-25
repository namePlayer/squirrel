<?php
declare(strict_types=1);

namespace App\Service\Economy;

use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\AccountUpdateFailedException;
use App\Exception\Account\MoneyCanNotBeLessThanZeroException;
use App\Exception\Account\MoneyCouldNotBeDepositedToAccountException;
use App\Exception\Account\MoneyCouldNotBeWithdrawnFromAccountException;
use App\Model\Account;
use App\Service\Account\AccountService;

class MoneyService
{

    public function __construct(
        private readonly AccountService $accountService
    )
    {
    }

    public function depositMoneyToAccount(int $accountId, int $amount): void
    {
        $account = $this->accountService->getAccountById($accountId);
        if(!$account instanceof Account)
        {
            throw new AccountNotFoundException();
        }

        $account->money += $amount;
        try {
            $this->accountService->update($account);
        }
        catch (AccountUpdateFailedException $e) {
            throw new MoneyCouldNotBeDepositedToAccountException();
        }
    }

    public function withdrawMoneyFromAccount(int $accountId, int $amount): void
    {
        $account = $this->accountService->getAccountById($accountId);
        if(!$account instanceof Account)
        {
            throw new AccountNotFoundException();
        }

        $account->money -= $amount;
        if($account->money < 0)
        {
            throw new MoneyCanNotBeLessThanZeroException();
        }

        try {
            $this->accountService->update($account);
        } catch (AccountUpdateFailedException $e) {
            throw new MoneyCouldNotBeWithdrawnFromAccountException();
        }
    }

    public function getAccountCurrentBalance(int $accountId): int
    {
        $account = $this->accountService->getAccountById($accountId);
        if(!$account instanceof Account)
        {
            throw new AccountNotFoundException();
        }

        return $account->money;
    }

}
