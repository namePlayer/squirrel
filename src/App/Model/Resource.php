<?php
declare(strict_types=1);

namespace App\Model;

class Resource
{

    public string $uid {
        get {
            return $this->uid;
        }
        set {
            $this->uid = $value;
        }
    }

    public static function hydrate(array $data): self
    {
        $self = new self();
        $self->uid = $data['uid'];
        return $self;
    }

    public function extract(): array
    {
        $self['uid'] = $this->uid;
        return $self;
    }

}
