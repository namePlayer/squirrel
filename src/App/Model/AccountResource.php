<?php
declare(strict_types=1);

namespace App\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AccountResource
{

    public UuidInterface $id {
        get {
            return $this->id;
        }
        set {
            $this->id = $value;
        }
    }
    public UuidInterface $account {
        get {
            return $this->account;
        }
        set {
            $this->account = $value;
        }
    }
    public string $resource {
        get {
            return $this->resource;
        }
        set {
            $this->resource = $value;
        }
    }
    public int $quantity {
        get {
            return $this->quantity;
        }
        set {
            $this->quantity = $value;
        }
    }

    public static function hydrate(array $data): AccountResource
    {
        $self = new self();
        $self->id = Uuid::fromString($data['id']);
        $self->account = Uuid::fromString($data['account']);
        $self->resource = $data['resource'];
        $self->quantity = $data['quantity'];
        return $self;
    }

}
