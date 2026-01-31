<?php
declare(strict_types=1);

namespace App\Enum\Resource;

enum ItemGroupEnum: string
{
    case Foraging = 'foraging';
    case Farming = 'farming';
    case Obtainable = 'obtainable';
    case Craftable = 'craftable';
    case Material = 'material';
    case Fruit = 'fruit';
    case TreeSapling = 'tree_sapling';
    case FruitTreeSapling = 'fruit_tree_sapling';
    case NotOfferedByMerchant = 'not_offered_by_merchant';

}
