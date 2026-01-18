<?php
declare(strict_types=1);

namespace App\Model;

use http\Params;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Account
{

    public UuidInterface $id {
        get {
            return $this->id;
        }
        set {
            $this->id = $value;
        }
    }
    public string $email {
        get {
            return $this->email;
        }
        set {
            $this->email = $value;
        }
    }
    public string $username {
        get {
            return $this->username;
        }
        set {
            $this->username = $value;
        }
    }
    public string $passwordHash {
        get {
            return $this->passwordHash;
        }
        set {
            $this->passwordHash = $value;
        }
    }

    public static function hydrate(array $data): Account
    {
        $model = new self();
        $model->id = Uuid::fromString($data['id']);
        $model->email = $data['email'];
        $model->username = $data['username'];
        $model->passwordHash = $data['passwordHash'];
        return $model;
    }

    public function extract(): array
    {
        $array['id'] = $this->id->toString();
        $array['email'] = $this->email;
        $array['username'] = $this->username;
        $array['passwordHash'] = $this->passwordHash;
        return $array;
    }

}
