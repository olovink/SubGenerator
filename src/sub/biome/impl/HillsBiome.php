<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\type\GrassyBiome;

class HillsBiome extends GrassyBiome {
    public const NAME = "Hills";
    protected float $baseHeight = 68.0;
    protected float $heightVariation = 25.0;

    public function getName(): string { return self::NAME; }
}