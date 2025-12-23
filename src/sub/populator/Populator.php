<?php

declare(strict_types=1);

namespace sub\populator;

use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

interface Populator{
    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void;

}