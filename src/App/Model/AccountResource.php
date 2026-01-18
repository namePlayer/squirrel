<?php
declare(strict_types=1);

namespace App\Model;

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
    public Account $account {
        get {
            return $this->account;
        }
        set {
            $this->account = $value;
        }
    }
    public Resource $item {
        get {
            return $this->item;
        }
        set {
            $this->item = $value;
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

}
