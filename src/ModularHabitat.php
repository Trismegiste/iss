<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator;

use Trismegiste\MapGenerator\Procedural\CellularAutomata;
use Trismegiste\MapGenerator\Procedural\SpaceStation;

/**
 * Facade for procedural generators
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

}
