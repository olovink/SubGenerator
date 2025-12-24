<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\type\GrassyBiome;

class IcePlainsBiome extends GrassyBiome{

    public const NAME = "IcePlains";

    protected float $baseHeight = 64.0;
    protected float $heightVariation = 8.0;

    public function getName(): string{
        return self::NAME;
    }
}