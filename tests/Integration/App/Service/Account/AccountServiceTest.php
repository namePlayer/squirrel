<?php

namespace Integration\App\Service\Account;

use App\DTO\Account\CreateAccountDTO;
use App\Exception\Account\DuplicateAccountEmailException;
use App\Model\Account;
use App\Service\Account\AccountService;
use App\Service\Account\PasswordService;
use App\Table\Account\AccountTable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Squirrel\Tests\IntegrationTestCase;

class AccountServiceTest extends IntegrationTestCase
{
    private AccountTable&MockObject $accountTableMock;
    private AccountService $accountService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountTableMock = $this->createMock(AccountTable::class);
        $this->container->add(AccountTable::class, $this->accountTableMock);

        $this->accountService = new AccountService(
            $this->accountTableMock,
            $this->container->get(PasswordService::class)
        );
    }

    #[Test]
    public function createWithDuplicateAccountEmailException(): void
    {
        $mail = 'test@example.local';
        $accountDto = new CreateAccountDTO($mail, 'test', 'password');

        $this->accountTableMock->method('findByEmail')->with($mail)->willReturn(new Account());

        $this->expectException(DuplicateAccountEmailException::class);

        $this->accountService->create($accountDto);
    }

    #[Test]
    public function create(): void
    {
        $mail = 'test@example.local';
        $accountDto = new CreateAccountDTO($mail, 'test', 'password');

        $this->accountTableMock->method('findByEmail')->willReturn(null);
        $this->accountTableMock->method('findBySlug')->willReturn(null);
        $this->accountTableMock->expects($this->once())->method('insert')->willReturnCallback(
            static function (Account $account) use ($mail): bool {
                return $account->email === $mail;
            }
        );

        $this->accountService->create($accountDto);
    }
}
