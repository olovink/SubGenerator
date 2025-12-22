<?php

declare(strict_types=1);

namespace sub\generator;

use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class SubGenerator extends Generator {

    public function __construct(array $settings = []){}

    public function generateChunk(int $chunkX, int $chunkZ): void{
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

        $chunk = $this->level->getChunk($chunkX, $chunkZ);

        for ($x = 0; $x < Chunk::EDGE_LENGTH; ++$x) {
            for ($z = 0; $z < Chunk::EDGE_LENGTH; ++$z) {

            }
        }
    }

    public function init(ChunkManager $level, Random $random): void{
        parent::init($level, $random);

        $this->random->setSeed($level->getSeed());
    }


    public function getSpawn(): Vector3{
        return new Vector3(0, 75, 0);
    }

    public function getName(): string{
        return "sub";
    }

    public function getSettings(): array{
        return [];
    }

    public function populateChunk(int $chunkX, int $chunkZ): void{
        //NOTHING HERE
    }
}