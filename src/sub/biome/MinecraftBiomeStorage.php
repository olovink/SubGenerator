<?php

declare(strict_types=1);

namespace sub\biome;

use pocketmine\level\biome\BiomeIds;
use sub\biome\impl\DesertBiome;
use sub\biome\impl\ForestBiome;
use sub\biome\impl\IcePlainsBiome;
use sub\biome\impl\JungleBiome;
use sub\biome\impl\HillsBiome;
use sub\biome\impl\OceanBiome;
use sub\biome\impl\PlainsBiome;
use sub\biome\impl\SwampBiome;
use sub\biome\impl\TaigaBiome;
use sub\biome\impl\UnknownBiome;

class MinecraftBiomeStorage {
    public const MAX_BIOMES = 256;

    private static ?\SplFixedArray $biomes = null;

    public static function init(): void{
        if(self::$biomes !== null){
            return;
        }

        self::$biomes = new \SplFixedArray(self::MAX_BIOMES);

        self::register(BiomeIds::OCEAN, new OceanBiome());
        self::register(BiomeIds::PLAINS, new PlainsBiome());
        self::register(BiomeIds::ICE_PLAINS, new IcePlainsBiome());
        self::register(BiomeIds::DESERT, new DesertBiome());
        self::register(BiomeIds::EXTREME_HILLS, new HillsBiome());
        self::register(BiomeIds::FOREST, new ForestBiome());
        self::register(BiomeIds::TAIGA, new TaigaBiome());
        self::register(BiomeIds::SWAMPLAND, new SwampBiome());
        self::register(BiomeIds::JUNGLE, new JungleBiome());
    }

    public static function register(int $id, BaseBiome $biome): void{
        self::$biomes[$id] = $biome;
        $biome->setId($id);
    }

    public static function getBiome(int $id): BaseBiome{
        if(self::$biomes === null){
            self::init();
        }

        if(self::$biomes[$id] === null){
            self::register($id, new UnknownBiome());
        }

        return self::$biomes[$id];
    }
}