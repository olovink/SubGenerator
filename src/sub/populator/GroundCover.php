<?php

declare(strict_types=1);

namespace sub\populator;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Liquid;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\utils\Random;

class GroundCover implements Populator
{
    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void
    {
        $chunk = $level->getChunk($chunkX, $chunkZ);
        for ($x = 0; $x < Chunk::EDGE_LENGTH; ++$x) {
            for ($z = 0; $z < Chunk::EDGE_LENGTH; ++$z) {
                $biome = Biome::getBiome($chunk->getBiomeId($x, $z));
                $cover = $biome->getGroundCover();
                if (count($cover) > 0) {
                    $diffY = 0;
                    if (!$cover[0]->isSolid()) {
                        $diffY = 1;
                    }

                    $startY = 127;
                    for (; $startY > 0; --$startY) {
                        if (!BlockFactory::fromFullBlock($chunk->getFullBlock($x, $startY, $z))->isTransparent()) {
                            break;
                        }
                    }
                    $startY = min(127, $startY + $diffY);
                    $endY = $startY - count($cover);
                    for ($y = $startY; $y > $endY && $y >= 0; --$y) {
                        $block = $cover[$startY - $y];
                        $id = BlockFactory::fromFullBlock($chunk->getFullBlock($x, $y, $z));
                        if ($id->getId() === Block::AIR && $block->isSolid()) {
                            break;
                        }
                        if ($block->canBeFlowedInto() && $id instanceof Liquid) {
                            continue;
                        }

                        $chunk->setFullBlock($x, $y, $z, $block->getFullId());
                    }
                }
            }
        }
    }
}
