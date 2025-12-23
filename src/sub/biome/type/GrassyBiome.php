<?php

declare(strict_types=1);

namespace sub\biome\type;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use sub\biome\BaseBiome;

abstract class GrassyBiome extends BaseBiome {
    protected float $baseHeight = 64.0;
    protected float $heightVariation = 8.0;
    protected float $temperature = 0.8;
    protected float $rainfall = 0.4;

    protected function generateGroundCover(): array{
        $grass = Block::get(BlockIds::GRASS);
        $dirt = Block::get(BlockIds::DIRT);

        return array_merge(
            array_fill(0, 1, $grass),
            array_fill(0, 4, $dirt)
        );
    }
}