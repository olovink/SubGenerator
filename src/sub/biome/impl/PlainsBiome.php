<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\BaseBiome;

class PlainsBiome extends BaseBiome {

    public const NAME = "Plains";

    protected float $baseHeight = 64.0;
    protected float $heightVariation = 8.0;

    public function getName(): string{
        return self::NAME;
    }

    protected function generateGroundCover(): array{
        return [];
    }
}