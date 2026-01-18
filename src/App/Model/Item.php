<?php
declare(strict_types=1);

namespace App\Model;

class Item
{

    public string $uid {
        get {
            return $this->uid;
        }
        set {
            $this->uid = $value;
        }
    }

}
