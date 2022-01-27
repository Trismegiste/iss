<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator;

/**
 * Description of SpaceStation
 *
 * @author flo
 */
class SpaceStation
{

    protected $side;
    protected $grid;

    public function __construct(int $side)
    {
        $this->side = $side;
        $this->grid = array_fill(0, $side, array_fill(0, $side, 0));
    }

    public function dump(): void
    {
        foreach ($this->grid as $row) {
            foreach ($row as $cell) {
                echo ($cell > 0) ? '██' : '  ';
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }

    public function set(int $x, int $y, int $grp): void
    {
        $this->grid[$x][$y] = $grp;
    }

    public function iterate(): void
    {
        $update = array_fill(0, $this->side, array_fill(0, $this->side, 0));

        foreach ($this->grid as $x => $col) {
            foreach ($col as $y => $cell) {
                $update[$x][$y] = $cell;
                if ($cell === 0) {
                    $neighbor = $this->crossCount($x, $y);
                    if (($neighbor > 0) && (random_int(0, 100) < (25 * $neighbor))) {
                        $update[$x][$y] = 1;
                    }
                }
            }
        }

        $this->grid = $update;
    }

    public function get(int $x, int $y): int
    {
        if (($x >= 0) && ($x < $this->side) && ($y >= 0) && ($y < $this->side)) {
            return $this->grid[$x][$y];
        }

        return 0;
    }

    public function neighborCount(int $x, int $y): int
    {
        $cpt = 0;
        for ($dx = -1; $dx <= 1; $dx++) {
            for ($dy = -1; $dy <= 1; $dy++) {
                $cpt += ($this->get($x + $dx, $y + $dy) > 0) ? 1 : 0;
            }
        }

        return $cpt;
    }

    public function crossCount(int $x, int $y): int
    {
        $cpt = 0;
        $cpt += ($this->get($x + 1, $y) > 0) ? 1 : 0;
        $cpt += ($this->get($x, $y + 1) > 0) ? 1 : 0;
        $cpt += ($this->get($x - 1, $y) > 0) ? 1 : 0;
        $cpt += ($this->get($x, $y - 1) > 0) ? 1 : 0;

        return $cpt;
    }

}
