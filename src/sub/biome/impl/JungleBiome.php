<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\type\GrassyBiome;

class JungleBiome extends GrassyBiome {
    public const NAME = "Jungle";
    protected float $baseHeight = 64.0;
    protected float $heightVariation = 25.0; // Очень холмисто

    public function getName(): string { return self::NAME; }
}