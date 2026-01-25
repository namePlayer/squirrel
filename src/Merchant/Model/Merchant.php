<?php
declare(strict_types=1);

namespace Merchant\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Merchant
{

    public int $id {
        get {
            return $this->id;
        }
        set {
            $this->id = $value;
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
    public UuidInterface $slug {
        get {
            return $this->slug;
        }
        set {
            $this->slug = $value;
        }
    }
    public int $price {
        get {
            return $this->price;
        }
        set {
            $this->price = $value;
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
    public \DateTime $expires {
        get {
            return $this->expires;
        }
        set {
            $this->expires = $value;
        }
    }

    public static function hydrate(array $data): self
    {
        $self = new self();
        $self->id = $data['id'];
        $self->resource = $data['resource'];
        $self->slug = Uuid::fromString($data['slug']);
        $self->price = $data['price'];
        $self->quantity = $data['quantity'];
        $self->expires = new \DateTime($data['expires']);
        return $self;
    }

    public function extract(bool $ignoreId = false): array
    {
        if(!$ignoreId){
            $self['id'] = $this->id;
        }
        $self['resource'] = $this->resource;
        $self['slug'] = $this->slug->toString();
        $self['price'] = $this->price;
        $self['quantity'] = $this->quantity;
        $self['expires'] = $this->expires->format('Y-m-d H:i:s');
        return $self;
    }

}
