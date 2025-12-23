<?php

declare(strict_types=1);

namespace sub\biome;

use pocketmine\level\biome\BiomeIds;
use pocketmine\Server;
use sub\biome\impl\PlainsBiome;
use sub\biome\impl\UnknownBiome;

class MinecraftBiomeStorage {
    public const MAX_BIOMES = 256;
    /** @var \SplFixedArray */
    private static \SplFixedArray $biomes;
    private static \PrefixedLogger $logger;

    public static function init(): void{
        self::$biomes = new \SplFixedArray(self::MAX_BIOMES);
        self::$logger = new \PrefixedLogger(Server::getInstance()->getLogger(), "BiomeStorage");

        self::register(BiomeIds::PLAINS, new PlainsBiome());
    }

    public static function register(int $id, IMinecraftBiome $biome): void{
        self::$biomes[$id] = $biome;
        $biome->setId($id);

        $biomeName = $biome->getName();
        self::$logger->info("Registered biome: $id ($biomeName)");
    }

    public static function getBiome(int $id) : IMinecraftBiome{
        if (self::$biomes[$id] === null) {
            self::register($id, new UnknownBiome());
        }

        return self::$biomes[$id];
    }

}