<?php

declare(strict_types=1);

namespace sub\biome;

interface IMinecraftBiome {

    public function getName(): string;

    public function getBaseHeight(): float;

    public function getHeightVariation(): float;

    public function getGroundCover(): array;

    public function getPopulators(): array;

    public function getId(): int;

    public function setId(int $id): void;

}