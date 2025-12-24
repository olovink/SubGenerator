<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\type\GrassyBiome;

class ForestBiome extends GrassyBiome {
    public const NAME = "Forest";
    protected float $baseHeight = 64.0;
    protected float $heightVariation = 18.0; // Холмистее

    public function getName(): string { return self::NAME; }
}