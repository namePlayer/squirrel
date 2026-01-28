<?php
declare(strict_types=1);

namespace App\Enum\Resource;

enum ItemGroupEnum: string
{
    case Obtainable = 'obtainable';
    case RawMaterial = 'raw_material';
    case Craftable = 'craftable';
    case Fruit = 'fruit';
    case FruitTreeSapling = 'fruit_tree_sapling';

}
