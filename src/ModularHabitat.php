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

    public function createOneBlockGenerator(int $side, int $iteration, int $capping): void
    {
        $gen = new SpaceStation($side);
        $gen->set($side / 2, $side / 2, 1);

        for ($idx = 0; $idx < $iteration; $idx++) {
            $gen->iterate();
        }
        //    $gen->roomIterationCapping($capping);
        $gen->roomIterationDivide(3);
        $door = new Procedural\DoorLayer($gen);
        $door->findDoor();
        $pop = new Procedural\NpcPopulator($gen);
        $pop->generate(50);
        $fog = new Procedural\FogOfWar($gen);

        $map = new RpgMap($gen);
        $map->appendLayer($door);
        $map->appendLayer($pop);
       // $map->appendLayer($fog);

        $map->printSvg();
    }

    public function createDistrictGenerator(int $sizePerBlock, int $blockCount, int $iteration, int $capping)
    {
        $side = $sizePerBlock * $blockCount;
        $gen = new SpaceStation($side);
        for ($col = $sizePerBlock / 2; $col < $side; $col += $sizePerBlock) {
            for ($row = $sizePerBlock / 2; $row < $side; $row += $sizePerBlock) {
                $gen->set($col, $row);
            }
        }

        for ($idx = 0; $idx < $iteration; $idx++) {
            $gen->iterate();
        }

        $gen->roomIterationCapping($capping);
        $door = new Procedural\DoorLayer($gen);
        $door->findDoor();
        $pop = new Procedural\NpcPopulator($gen);
        $pop->generate(30);

        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"800\" height=\"800\" viewBox=\"0 0 $side $side\">";
        echo "<rect x=\"0\" y=\"0\" width=\"$side\" height=\"$side\" fill=\"white\"/>";
        $gen->printSvg();
        $door->printSvg();
        $pop->printSvg();
        echo '</svg>';
    }

    public function createStreetGenerator(int $streetWidth, int $streetCount, int $iteration, int $capping)
    {
        $side = $streetWidth * $streetCount;
        $gen = new SpaceStation($side);
        for ($col = $streetWidth / 2; $col < $side; $col += $streetWidth) {
            for ($row = 1; $row < $side - 1; $row++) {
                $gen->set($col, $row);
            }
        }

        for ($idx = 0; $idx < $iteration; $idx++) {
            $gen->iterate();
        }

        $gen->roomIterationCapping($capping);
        $door = new Procedural\DoorLayer($gen);
        $door->findDoor();
        $pop = new Procedural\NpcPopulator($gen);
        $pop->generate(300);

        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"800\" height=\"800\" viewBox=\"0 0 $side $side\">";
        echo "<rect x=\"0\" y=\"0\" width=\"$side\" height=\"$side\" fill=\"white\"/>";
        $gen->printSvg();
        $door->printSvg();
        $pop->printSvg();
        echo '</svg>';
    }

}
