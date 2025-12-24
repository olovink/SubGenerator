<?php

declare(strict_types=1);

namespace sub\biome;

use pocketmine\block\Block;
use pocketmine\level\biome\BiomeIds;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use sub\populator\Populator;

abstract class BaseBiome {
    protected int $id = BiomeIds::OCEAN;
    protected float $baseHeight = 64.0;
    protected float $heightVariation = 16.0;

    /** @var Populator[] */
    protected array $populators = [];

    /** @var Block[] */
    protected array $groundCover = [];

    public function __construct(){
        $this->setGroundCover($this->generateGroundCover());
    }

    /**
     * Устанавливает покрытие поверхности
     * @param Block[] $cover
     */
    protected function setGroundCover(array $cover): void{
        $this->groundCover = $cover;
    }

    /**
     * Генерирует покрытие поверхности (должен быть переопределен в дочерних классах)
     * @return Block[]
     */
    abstract protected function generateGroundCover(): array;

    /**
     * Получает покрытие поверхности
     * @return Block[]
     */
    public function getGroundCover(): array{
        return $this->groundCover;
    }

    public function getBaseHeight(): float{
        return $this->baseHeight;
    }

    public function getHeightVariation(): float{
        return $this->heightVariation;
    }

    public function addPopulator(Populator $populator): void{
        $this->populators[] = $populator;
    }

    public function getPopulators(): array{
        return $this->populators;
    }

    public function getId(): int{
        return $this->id;
    }

    public function setId(int $id): void{
        $this->id = $id;
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ, Random $random) : void{
        foreach ($this->populators as $populator) {
            $populator->populate($world, $chunkX, $chunkZ, $random);
        }
    }

}