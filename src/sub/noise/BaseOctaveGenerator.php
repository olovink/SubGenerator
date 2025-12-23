<?php

declare(strict_types=1);

namespace sub\noise;

abstract class BaseOctaveGenerator{

    public float $x_scale = 1.0;
    public float $y_scale = 1.0;
    public float $z_scale = 1.0;

    /**
     * @param NoiseGenerator[] $octaves
     */
    protected function __construct(
        protected array $octaves
    ){}

    /**
     * Задает масштаб, используемый для всех координат, передаваемых этому генератору.
     * <p>
     * Это эквивалентно установке для каждой координаты указанного значения.
     *
     * @param float $scale Новое значение для масштабирования каждой координаты
     */
    public function setScale(float $scale) : void{
        $this->x_scale = $scale;
        $this->y_scale = $scale;
        $this->z_scale = $scale;
    }

    /**
     * Получает клон отдельных октав, используемых в этом генераторе
     *
     * @return NoiseGenerator[] клон отдельных октав
     */
    public function getOctaves() : array{
        $octaves = [];
        foreach($this->octaves as $key => $value){
            $octaves[$key] = clone $value;
        }

        return $octaves;
    }
}