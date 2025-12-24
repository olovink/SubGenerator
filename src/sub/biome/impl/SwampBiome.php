<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\type\GrassyBiome;

class SwampBiome extends GrassyBiome {
    public const NAME = "Swamp";
    protected float $baseHeight = 62.0;
    protected float $heightVariation = 4.0; // Очень плоско

    public function getName(): string { return self::NAME; }
}