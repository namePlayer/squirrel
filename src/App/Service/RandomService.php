<?php
declare(strict_types=1);

namespace App\Service;

class RandomService
{

    public function generateRandomIntegerInRange(int $min, int $max): int
    {
        return rand($min, $max);
    }

}
