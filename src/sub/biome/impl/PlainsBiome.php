<?php

declare(strict_types=1);

namespace sub\biome\impl;

use sub\biome\MinecraftBiome;

class PlainsBiome implements MinecraftBiome {
    public const NAME = "Plains";




    public function getName(): string{
        return self::NAME;
    }
}