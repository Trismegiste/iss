<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

/**
 * An abstract cellular automaton
 */
abstract class GenericAutomaton implements CellularAutomaton
{

    protected $side;
    protected $grid;

    public function __construct(int $side)
    {
        $this->side = $side;
        $this->grid = array_fill(0, $side, array_fill(0, $side, 0));
    }

    public function getGrid(): array
    {
        return $this->grid;
    }

    public function getSize(): int
    {
        return $this->side;
    }

    public function groupByLevel(): array
    {
        $group = [];
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                $cell = $this->grid[$x][$y];
                if ($cell !== 0) {
                    $group[$cell][] = ['x' => $x, 'y' => $y];
                }
            }
        }

        krsort($group);

        return $group;
    }

    public function set(int $x, int $y, int $grp = 1): void
    {
        $this->grid[$x][$y] = $grp;
    }

}
