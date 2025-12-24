<?php

declare(strict_types=1);

namespace sub\generator;

use pocketmine\block\BlockIds;
use pocketmine\level\biome\Biome;
use pocketmine\level\biome\BiomeIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use sub\biome\MinecraftBiomeStorage;
use sub\noise\SimplexOctaveGenerator;
use sub\populator\GroundCover;
use sub\populator\Populator;

class SubGenerator extends Generator {

    private SimplexOctaveGenerator $continentalNoise;
    private SimplexOctaveGenerator $roughnessNoise;
    private SimplexOctaveGenerator $detailNoise;

    private SimplexOctaveGenerator $temperatureNoise;
    private SimplexOctaveGenerator $rainfallNoise;

    private array $heightMap = [];
    private array $biomeMap = [];

    /** @var Populator[] */
    private array $generationPopulators = [];

    public function __construct(array $settings = []){
        parent::__construct();
    }

    public function init(ChunkManager $level, Random $random): void{
        parent::init($level, $random);

        $this->random->setSeed($this->level->getSeed());

        $this->continentalNoise = new SimplexOctaveGenerator($this->random, 6);
        $this->continentalNoise->setScale(1 / 768.0);

        $this->roughnessNoise = new SimplexOctaveGenerator($this->random, 6);
        $this->roughnessNoise->setScale(1 / 384.0);

        $this->detailNoise = new SimplexOctaveGenerator($this->random, 4);
        $this->detailNoise->setScale(1 / 128.0);

        $this->temperatureNoise = new SimplexOctaveGenerator($this->random, 4);
        $this->temperatureNoise->setScale(1 / 1500.0);

        $this->rainfallNoise = new SimplexOctaveGenerator($this->random, 4);
        $this->rainfallNoise->setScale(1 / 1500.0);


        $this->generationPopulators[] = new GroundCover();
    }

    public function generateChunk(int $chunkX, int $chunkZ): void{
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
        $chunk = $this->level->getChunk($chunkX, $chunkZ);

        $this->generateHeightMap($chunkX, $chunkZ);

        $seaLevel = 63;

        for($x = 0; $x < 16; ++$x){
            for($z = 0; $z < 16; ++$z){
                $height = $this->heightMap[$x][$z];
                $biomeId = $this->biomeMap[$x][$z];

                $chunk->setBiomeId($x, $z, $biomeId);

                $chunk->setBlockId($x, 0, $z, BlockIds::BEDROCK);

                for($y = 1; $y <= $height; ++$y){
                    $chunk->setBlockId($x, $y, $z, BlockIds::STONE);
                }

                for($y = $height + 1; $y <= $seaLevel; ++$y){
                    $chunk->setBlockId($x, $y, $z, BlockIds::STILL_WATER);
                }
            }
        }

        foreach ($this->generationPopulators as $populator) {
            $populator->populate($this->level, $chunkX, $chunkZ, $this->random);
        }
    }

    private function generateHeightMap(int $chunkX, int $chunkZ): void{
        $startX = $chunkX * 16;
        $startZ = $chunkZ * 16;

        // Весы для сглаживания (3x3 окно, центр — 4, углы — 1, стороны — 2)
        $biomeWeights = [1, 2, 1, 2, 4, 2, 1, 2, 1];

        for($x = 0; $x < 16; ++$x){
            for($z = 0; $z < 16; ++$z){
                $worldX = $startX + $x;
                $worldZ = $startZ + $z;

                // Температура/влажность
                $temp = ($this->temperatureNoise->octaveNoise($worldX, $worldZ, 0, 2.0, 0.5, true) + 1) / 2;
                $rain = ($this->rainfallNoise->octaveNoise($worldX, $worldZ, 0, 2.0, 0.5, true) + 1) / 2;

                $biomeId = $this->getBiomeId($temp, $rain);
                $this->biomeMap[$x][$z] = $biomeId;

                // === Сглаживание биомных параметров ===
                $heightVariationSum = 0.0;
                $baseHeightSum = 0.0;
                $weightSum = 0.0;

                $wi = 0;
                for($sx = -1; $sx <= 1; ++$sx){
                    for($sz = -1; $sz <= 1; ++$sz){
                        $sampleX = $worldX + $sx * 4; // Шаг 4, как в примере (низкая резолюция)
                        $sampleZ = $worldZ + $sz * 4;

                        $sampleTemp = ($this->temperatureNoise->octaveNoise($sampleX, $sampleZ, 0, 2.0, 0.5, true) + 1) / 2;
                        $sampleRain = ($this->rainfallNoise->octaveNoise($sampleX, $sampleZ, 0, 2.0, 0.5, true) + 1) / 2;

                        $sampleBiomeId = $this->getBiomeId($sampleTemp, $sampleRain);
                        $sampleBiome = MinecraftBiomeStorage::getBiome($sampleBiomeId);

                        $weight = $biomeWeights[$wi++];
                        $heightVariationSum += $sampleBiome->getHeightVariation() * $weight;
                        $baseHeightSum += $sampleBiome->getBaseHeight() * $weight;
                        $weightSum += $weight;
                    }
                }

                $smoothedVariation = $heightVariationSum / $weightSum;
                $smoothedBase = $baseHeightSum / $weightSum;

                // === Общий шум (как раньше, но можно добавить depth noise для континентов) ===
                $continental = $this->continentalNoise->octaveNoise($worldX, $worldZ, 0, 2.0, 0.5, false) * 20;
                $rough = $this->roughnessNoise->octaveNoise($worldX, $worldZ, 0, 2.0, 0.5, false) * 10;
                $detail = $this->detailNoise->octaveNoise($worldX, $worldZ, 0, 2.0, 0.5, false) * 3;

                // ... внутри цикла по x,z

                $rawHeightOffset = $continental + $rough + $detail;

                $finalHeight = (int) ($smoothedBase + $rawHeightOffset * ($smoothedVariation / 28.0));

                $finalHeight = max(40, min(180, $finalHeight));

                $this->heightMap[$x][$z] = $finalHeight;
            }
        }
    }

    private function getBiomeId(float $temperature, float $rainfall): int{
        // Сначала проверяем специальные условия для холмов — в "средней" зоне, но с чуть большей вариацией
        if ($temperature > 0.35 && $temperature < 0.65 && $rainfall > 0.3 && $rainfall < 0.7) {
            return BiomeIds::EXTREME_HILLS; // Или ваш custom ID для HillsBiome
        }

        // Основные равнины — теперь меньше, но всё ещё много
        if ($temperature > 0.3 && $temperature < 0.7 && $rainfall > 0.2 && $rainfall < 0.6) {
            return BiomeIds::PLAINS;
        }

        if ($temperature < 0.25) {
            return BiomeIds::ICE_PLAINS;
        }
        if ($temperature > 0.75) {
            if ($rainfall < 0.25) return BiomeIds::DESERT;
            if ($rainfall > 0.7) return BiomeIds::JUNGLE;
            return BiomeIds::SAVANNA;
        }
        if ($rainfall > 0.7) return BiomeIds::SWAMPLAND;
        if ($temperature < 0.4) return BiomeIds::TAIGA;
        if ($rainfall > 0.5) return BiomeIds::FOREST;

        return BiomeIds::PLAINS;
    }

    public function populateChunk(int $chunkX, int $chunkZ): void{
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $biome = MinecraftBiomeStorage::getBiome($chunk->getBiomeId(7, 7));
        $biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
    }

    public function getSpawn(): Vector3{
        return new Vector3(0, 80, 0);
    }

    public function getName(): string{
        return "sub";
    }

    public function getSettings(): array{
        return [];
    }
}