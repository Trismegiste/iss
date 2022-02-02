<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator;

use Trismegiste\MapGenerator\Procedural\CellularAutomaton;
use Trismegiste\MapGenerator\Procedural\SpaceStation;

/**
 * Facade for procedural generators
 * @refactor : must think in layers of <g>
 */
class ModularHabitat
{

    protected function drawSquare(float $x, float $y, float $size, string $color): void
    {
        echo "<rect x=\"$x\" y=\"$y\" width=\"$size\" height=\"$size\" fill=\"$color\"/>";
    }

    public function createOneBlockGenerator(int $side, int $iteration, int $capping): void
    {
        $gen = new SpaceStation($side);
        $gen->set($side / 2, $side / 2, 1);

        for ($idx = 0; $idx < $iteration; $idx++) {
            $gen->iterate();
        }
        $gen->roomIterationCapping($capping);
        $door = new Procedural\DoorLayer($gen);
        $door->findDoor();
        $pop = new Procedural\NpcPopulator($gen);
        $pop->generate(20);

        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"800\" height=\"800\" viewBox=\"0 0 $side $side\">";
        $this->drawSquare(0, 0, $side, 'white');
        $gen->printSvg();
        $door->printSvg();
        $pop->printSvg();
        echo '</svg>';
    }

    public function createDistrictGenerator(int $sizePerBlock, int $blockCount, int $iteration, int $capping): CellularAutomaton
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

    public function createStreetGenerator(int $streetWidth, int $streetCount, int $iteration, int $capping): CellularAutomaton
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
