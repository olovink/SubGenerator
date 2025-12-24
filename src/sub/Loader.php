<?php

declare(strict_types=1);

namespace sub;

use pocketmine\level\generator\GeneratorManager;
use pocketmine\plugin\PluginBase;
use sub\biome\MinecraftBiomeStorage;
use sub\generator\SubGenerator;

class Loader extends PluginBase {

    public function onLoad(): void{
        GeneratorManager::addGenerator(SubGenerator::class, "sub");
    }

    public function onEnable(): void{
        foreach (GeneratorManager::getGeneratorList() as $generator) {
            $this->getLogger()->info("Generator: $generator");
        }
        MinecraftBiomeStorage::init();
    }

    public function onDisable(): void{}
}