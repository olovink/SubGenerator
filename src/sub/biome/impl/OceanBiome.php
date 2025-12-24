<?php

declare(strict_types=1);

namespace sub\biome\impl;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use sub\biome\BaseBiome;
use sub\biome\type\WateryBiome;

class OceanBiome extends WateryBiome {
    public const NAME = "Ocean";
    protected float $baseHeight = 50.0;
    protected float $heightVariation = 6.0;

    public function getName(): string { return self::NAME; }
}