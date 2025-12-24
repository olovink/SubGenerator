<?php

declare(strict_types=1);

namespace sub\biome\type;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use sub\biome\BaseBiome;

abstract class WateryBiome extends BaseBiome {
    protected function generateGroundCover(): array {
        $sand = Block::get(BlockIds::SAND);
        $gravel = Block::get(BlockIds::GRAVEL);
        return [$sand, $sand, $gravel, $gravel];
    }
}