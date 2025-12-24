<?php

declare(strict_types=1);

namespace sub\biome\impl;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use sub\biome\BaseBiome;

class DesertBiome extends BaseBiome {
    public const NAME = "Desert";
    protected float $baseHeight = 64.0;
    protected float $heightVariation = 8.0;

    public function getName(): string { return self::NAME; }

    protected function generateGroundCover(): array {
        $sand = Block::get(BlockIds::SAND);
        return array_fill(0, 6, $sand);
    }
}