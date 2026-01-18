<?php
declare(strict_types=1);

namespace App\Validator;

use App\DTO\Account\CreateAccountDTO;
use App\Exception\Account\EmailTooLongException;
use App\Exception\Account\InvalidEmailException;
use App\Exception\Account\UsernameTooLongException;
use App\Exception\Account\UsernameTooShortException;
use App\Software;

class AccountRegistrationValidator
{

    public function validate(CreateAccountDTO $createAccountDTO): void
    {
        if(mb_strlen($createAccountDTO->email) > Software::MAX_EMAIL_LENGTH)
        {
            throw new EmailTooLongException();
        }

        if(filter_var($createAccountDTO->email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidEmailException();
        }

        $usernameLength = mb_strlen($createAccountDTO->username);
        if($usernameLength < Software::MIN_USERNAME_LENGTH) {
            throw new UsernameTooShortException();
        }

        if($usernameLength > Software::MAX_USERNAME_LENGTH) {
            throw new UsernameTooLongException();
        }
    }

}
