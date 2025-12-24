<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\type\GrassyBiome;

class TaigaBiome extends GrassyBiome {
    public const NAME = "Taiga";
    protected float $baseHeight = 64.0;
    protected float $heightVariation = 15.0;

    public function getName(): string { return self::NAME; }
}