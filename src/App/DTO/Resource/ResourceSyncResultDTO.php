<?php
declare(strict_types=1);

namespace App\DTO\Resource;

class ResourceSyncResultDTO
{

    public function __construct(
        public int $created,
        public int $exists,
        public bool $success
    )
    {
    }

}
