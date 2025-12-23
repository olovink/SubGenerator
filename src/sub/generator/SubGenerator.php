<?php

declare(strict_types=1);

namespace sub\generator;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use sub\noise\SimplexOctaveGenerator;

class SubGenerator extends Generator {

    private SimplexOctaveGenerator $heightNoise;

    private SimplexOctaveGenerator $roughnessNoise;

    private SimplexOctaveGenerator $roughnessNoise2;

    private SimplexOctaveGenerator $detailNoise;

    private SimplexOctaveGenerator $groundNoise;

    private array $heightMap = [];

    public function __construct(array $settings = []){
        parent::__construct();
    }

    public function init(ChunkManager $level, Random $random): void{
        parent::init($level, $random);

        $this->random->setSeed($this->level->getSeed());

        // Инициализация генераторов шума для высоты и рельефа
        $this->heightNoise = new SimplexOctaveGenerator($this->random, 16);
        $this->heightNoise->setScale(1 / 200.0);

        $this->roughnessNoise = new SimplexOctaveGenerator($this->random, 16);
        $this->roughnessNoise->setScale(1 / 450.0);

        $this->roughnessNoise2 = new SimplexOctaveGenerator($this->random, 16);
        $this->roughnessNoise2->setScale(1 / 45.0);

        $this->detailNoise = new SimplexOctaveGenerator($this->random, 16);
        $this->detailNoise->setScale(1 / 12.0);

        $this->groundNoise = new SimplexOctaveGenerator($this->random, 8);
        $this->groundNoise->setScale(1 / 100.0);
    }

    public function generateChunk(int $chunkX, int $chunkZ): void{
        $chunk = $this->level->getChunk($chunkX, $chunkZ);

        // Создаем карту высот для чанка
        $this->generateHeightMap($chunkX, $chunkZ);

        $waterHeight = 63;
        $sandDepth = 5; // Глубина песка

        for($x = 0; $x < 16; ++$x){
            for($z = 0; $z < 16; ++$z){
                $height = $this->heightMap[$x][$z];
                // Бедрок внизу
                $chunk->setBlockId($x, 0, $z, BlockIds::BEDROCK);

                // Определяем, должен ли быть здесь песок
                $isSandArea = $height <= $waterHeight + 2;

                if($isSandArea){
                    // Песочная зона (5 блоков песка вниз)
                    $sandBottom = max(1, $height - $sandDepth + 1);

                    // Камень под песком
                    for($y = 1; $y < $sandBottom; ++$y){
                        $chunk->setBlockId($x, $y, $z, BlockIds::STONE);
                    }

                    // Песок (5 блоков)
                    for($y = $sandBottom; $y <= $height; ++$y){
                        $chunk->setBlockId($x, $y, $z, BlockIds::SAND);
                    }
                } else {
                    // Обычная земляная зона
                    // Камень от бедрока до 4 блоков под поверхностью
                    $stoneTop = $height - 4;
                    for($y = 1; $y < $stoneTop; ++$y){
                        $chunk->setBlockId($x, $y, $z, BlockIds::STONE);
                    }

                    // Земля (3 блока)
                    for($y = max(1, $stoneTop); $y < $height; ++$y){
                        $chunk->setBlockId($x, $y, $z, BlockIds::DIRT);
                    }

                    // Трава сверху
                    $chunk->setBlockId($x, $height, $z, BlockIds::GRASS);
                }

                // Вода на уровне ниже 63
                for($y = $height + 1; $y <= $waterHeight; ++$y){
                    $chunk->setBlockId($x, $y, $z, BlockIds::STILL_WATER);
                }
            }
        }
    }

    private function generateHeightMap(int $chunkX, int $chunkZ): void{
        $startX = $chunkX * 16;
        $startZ = $chunkZ * 16;

        for($x = 0; $x < 16; ++$x){
            for($z = 0; $z < 16; ++$z){
                $worldX = $startX + $x;
                $worldZ = $startZ + $z;

                // Базовый шум высоты
                $height = $this->heightNoise->octaveNoise($worldX, $worldZ, 0, 1.0, 0.2, false) * 4;

                // Добавляем шероховатость
                $roughness = $this->roughnessNoise->octaveNoise($worldX, $worldZ, 0, 1.0, 0.5, false) * 4;
                $roughness2 = $this->roughnessNoise2->octaveNoise($worldX, $worldZ, 0, 1.0, 0.5, false) * 2;

                // Детализация
                $detail = $this->detailNoise->octaveNoise($worldX, $worldZ, 0, 1.0, 0.2, false);

                // Комбинируем все факторы
                $combinedHeight = $height + $roughness + $roughness2 + $detail;

                // Масштабируем и добавляем базовую высоту
                $finalHeight = (int)($combinedHeight * 4 + 64);

                $finalHeight = max(40, min(128, $finalHeight));

                $this->heightMap[$x][$z] = $finalHeight;
            }
        }
    }

    public function getSpawn(): Vector3{
        return new Vector3(0, $this->heightMap[8][8] + 1, 0);
    }

    public function getName(): string{
        return "sub";
    }

    public function getSettings(): array{
        return [];
    }

    public function populateChunk(int $chunkX, int $chunkZ): void{}
}