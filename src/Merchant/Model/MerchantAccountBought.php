<?php
declare(strict_types=1);

namespace Merchant\Model;

class MerchantAccountBought
{

    public int $merchant {
        get {
            return $this->merchant;
        }
        set {
            $this->merchant = $value;
        }
    }
    public int $account {
        get {
            return $this->account;
        }
        set {
            $this->account = $value;
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

    public static function hydrate(array $data): self
    {
        $self = new self();
        $self->merchant = $data['merchant'];
        $self->account = $data['account'];
        $self->quantity = $data['quantity'];
        return $self;
    }

    public function extract(): array
    {
        $data['merchant'] = $this->merchant;
        $data['account'] = $this->account;
        $data['quantity'] = $this->quantity;
        return $data;
    }

}
