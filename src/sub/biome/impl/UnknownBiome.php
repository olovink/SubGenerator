<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\BaseBiome;

class UnknownBiome extends BaseBiome {
    public const NAME = "Unknown";

    protected function generateGroundCover(): array{
        return [];
    }

    public function getName(): string{
        return self::NAME;
    }
}