<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator;

use Trismegiste\MapGenerator\Procedural\CellularAutomata;
use Trismegiste\MapGenerator\Procedural\SpaceStation;

/**
 * Facade for procedural generators
 * @refactor : must think in layers of <g>
 */
class ModularHabitat
{

    public function createOneBlockGenerator(int $side, int $iteration, int $capping): CellularAutomata
    {
        $gen = new SpaceStation($side);
        $gen->set($side / 2, $side / 2, 1);

        for ($idx = 0; $idx < $iteration; $idx++) {
            $gen->iterate();
        }

        $gen->roomIterationCapping($capping);
        $gen->findDoor();

        return $gen;
    }

    public function createDistrictGenerator(int $sizePerBlock, int $blockCount, int $iteration, int $capping): CellularAutomata
    {
        $gen = new SpaceStation($sizePerBlock * $blockCount);
        for ($col = $sizePerBlock / 2; $col < $blockCount * $sizePerBlock; $col += $sizePerBlock) {
            for ($row = $sizePerBlock / 2; $row < $blockCount * $sizePerBlock; $row += $sizePerBlock) {
                $gen->set($col, $row);
            }
        }

        for ($idx = 0; $idx < $iteration; $idx++) {
            $gen->iterate();
        }

        $gen->roomIterationCapping($capping);
        $gen->findDoor();

        return $gen;
    }

    public function createStreetGenerator(int $streetWidth, int $streetCount, int $iteration, int $capping): CellularAutomata
    {
        $gen = new SpaceStation($streetWidth * $streetCount);
        for ($col = $streetWidth / 2; $col < $streetCount * $streetWidth; $col += $streetWidth) {
            for ($row = 1; $row < $streetCount * $streetWidth - 1; $row++) {
                $gen->set($col, $row);
            }
        }

        for ($idx = 0; $idx < $iteration; $idx++) {
            $gen->iterate();
        }

        $gen->roomIterationCapping($capping);
        $gen->findDoor();

        return $gen;
    }

}
