<?php

declare(strict_types=1);

namespace sub\populator;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Leaves;
use pocketmine\block\TallGrass;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\utils\Random;

class SmallGrass implements Populator {

    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void{
        $block = new TallGrass();
        // 8 попыток на чанк, как в vanilla — достаточно для естественной плотности
        for ($i = 0; $i < 8; ++$i) {
            $x = $random->nextRange($chunkX * Chunk::EDGE_LENGTH, $chunkX * Chunk::EDGE_LENGTH + (Chunk::EDGE_LENGTH - 1));
            $z = $random->nextRange($chunkZ * Chunk::EDGE_LENGTH, $chunkZ * Chunk::EDGE_LENGTH + (Chunk::EDGE_LENGTH - 1));
            $y = $this->getHighestWorkableBlock($level, $x, $z);

            if ($y !== -1 && $this->canTallGrassStay($level, $x, $y, $z)) {
                $level->setBlockAt($x, $y, $z, $block);
            }
        }
    }

    private function canTallGrassStay(ChunkManager $level, int $x, int $y, int $z) : bool{
        $b = $level->getBlockAt($x, $y, $z)->getId();
        return ($b === BlockIds::AIR || $b === BlockIds::SNOW_LAYER) && $level->getBlockAt($x, $y - 1, $z)->getId() === BlockIds::GRASS;
    }

    private function getHighestWorkableBlock(ChunkManager $level, int $x, int $z) : int{
        $highestBlock = $level->getChunk($x >> Chunk::COORD_BIT_SIZE, $z >> Chunk::COORD_BIT_SIZE)?->getHighestBlockAt($x & Chunk::COORD_MASK, $z & Chunk::COORD_MASK);
        if($highestBlock === null){
            return -1;
        }

        for($y = $highestBlock; $y >= 0; --$y){
            $b = $level->getBlockAt($x, $y, $z);
            if($b->getId() !== BlockIds::AIR && !($b instanceof Leaves) && $b->getId() !== BlockIds::SNOW_LAYER){
                return $y + 1;
            }
        }

        return -1;
    }
}