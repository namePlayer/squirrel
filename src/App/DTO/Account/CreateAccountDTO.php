<?php
declare(strict_types=1);

namespace App\DTO\Account;

class CreateAccountDTO
{

    public function __construct(
        public readonly string $email,
        public readonly string $username,
        public readonly string $password
    )
    {
    }

}
