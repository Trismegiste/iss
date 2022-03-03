<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator;

use Trismegiste\MapGenerator\Procedural\DoorLayer;
use Trismegiste\MapGenerator\Procedural\FogOfWar;
use Trismegiste\MapGenerator\Procedural\NpcPopulator;
use Trismegiste\MapGenerator\Procedural\SpaceStation;

/**
 * Facade for procedural generators
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
        $gen->blurry();
        $gen->iterate();

        $door = new DoorLayer($gen);
        $door->findDoor();
        $pop = new NpcPopulator($gen);
        $pop->generate(50, 0);
        $fog = new FogOfWar($gen);

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
        $door = new DoorLayer($gen);
        $door->findDoor();
        $pop = new NpcPopulator($gen);
        $pop->generate(30, 0);

        $map = new RpgMap($gen);
        $map->appendLayer($door);
        $map->appendLayer($pop);
        $map->printSvg();
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
        $door = new DoorLayer($gen);
        $door->findDoor();
        $pop = new NpcPopulator($gen);
        $pop->generate(300, 0);

        $map = new RpgMap($gen);
        $map->appendLayer($door);
        $map->appendLayer($pop);
        $map->printSvg();
    }

}
