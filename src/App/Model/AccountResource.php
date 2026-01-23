<?php
declare(strict_types=1);

namespace App\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AccountResource
{

    public int $account {
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
        $self->account = $data['account'];
        $self->resource = $data['resource'];
        $self->quantity = $data['quantity'];
        return $self;
    }

    public function extract(): array
    {
        $self['account'] = $this->account;
        $self['resource'] = $this->resource;
        $self['quantity'] = $this->quantity;
        return $self;
    }

}
