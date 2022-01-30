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
    protected $door;

    public function __construct(int $side)
    {
        $this->side = $side;
        $this->grid = array_fill(0, $side, array_fill(0, $side, 0));
    }

    public function set(int $x, int $y, int $grp): void
    {
        $this->grid[$x][$y] = $grp;
    }

    public function iterate(): void
    {
        $update = array_fill(0, $this->side, array_fill(0, $this->side, 0));

        for ($x = 1; $x < $this->side - 1; $x++) {
            for ($y = 1; $y < $this->side - 1; $y++) {
                $cell = $this->grid[$x][$y];
                $update[$x][$y] = $cell; // default value since we'll have random update

                if ($cell === 0) {
                    $neighbor = $this->crossCount($x, $y);
                    if (($neighbor > 0) && (random_int(0, 100) < (25 * $neighbor))) {
                        $update[$x][$y] = 1;
                    }
                } else {
                    $neighbor = $this->neighborCount($x, $y);
                    if ($neighbor === 8) {
                        if (random_int(0, 100) < 25) {
                            $update[$x][$y] = $cell + 1;
                        }
                    }
                }
            }
        }

        $this->grid = $update;
    }

    public function neighborCount(int $x, int $y): int
    {
        $cpt = ($this->grid[$x][$y] > 0) ? -1 : 0;

        for ($dx = -1; $dx <= 1; $dx++) {
            for ($dy = -1; $dy <= 1; $dy++) {
                $cpt += ($this->grid[$x + $dx][$y + $dy] > 0) ? 1 : 0;
            }
        }

        return $cpt;
    }

    public function crossCount(int $x, int $y): int
    {
        $cpt = ($this->grid[$x + 1][$y] > 0) ? 1 : 0;
        $cpt += ($this->grid[$x - 1][$y] > 0) ? 1 : 0;
        $cpt += ($this->grid[$x][$y + 1] > 0) ? 1 : 0;
        $cpt += ($this->grid[$x][$y - 1] > 0) ? 1 : 0;

        return $cpt;
    }

    public function dumpSvg(): void
    {
        $scale = 32;
        $width = $this->side * $scale;
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"$width\" height=\"$width\">";

        for ($x = 1; $x < $this->side; $x++) {
            for ($y = 1; $y < $this->side; $y++) {
                $cell = $this->grid[$x][$y];
                $x0 = $scale * $x;
                $y0 = $scale * $y;
                $style = "stroke: black; stroke-width: 3";

                if ($cell !== $this->grid[$x][$y - 1]) {
                    $x2 = $x0 + $scale;
                    echo "<line x1=\"$x0\" y1=\"$y0\" x2=\"$x2\" y2=\"$y0\" style=\"$style\"/>";
                }

                if ($cell !== $this->grid[$x - 1][$y]) {
                    $y2 = $y0 + $scale;
                    echo "<line x1=\"$x0\" y1=\"$y0\" x2=\"$x0\" y2=\"$y2\" style=\"$style\"/>";
                }
            }
        }


        $style = "stroke: green; stroke-width: 5";
        foreach ($this->door as $x => $col) {
            foreach ($col as $y => $door) {
                $x0 = $scale * $x;
                $y0 = $scale * $y;
                if ($door['W']) {
                    $y1 = $y0 + $scale / 3;
                    $y2 = $y0 + $scale * 2 / 3;
                    echo "<line x1=\"$x0\" y1=\"$y1\" x2=\"$x0\" y2=\"$y2\" style=\"$style\"/>";
                }
                if ($door['N']) {
                    $x1 = $x0 + $scale / 3;
                    $x2 = $x0 + $scale * 2 / 3;
                    echo "<line x1=\"$x1\" y1=\"$y0\" x2=\"$x2\" y2=\"$y0\" style=\"$style\"/>";
                }
            }
        }

        echo '</svg>';
    }

    public function roomIterationCapping(int $threshold): void
    {
        foreach ($this->grid as $x => $col) {
            foreach ($col as $y => $cell) {
                if ($cell > $threshold) {
                    $this->grid[$x][$y] = $threshold;
                }
            }
        }
    }

    public function findDoor()
    {
        $this->door = array_fill(0, $this->side, array_fill(0, $this->side, ['N' => false, 'W' => false]));
        $squaresPerRoomPerLevel = $this->groupSplitting($this->groupByLevel());

        foreach ($squaresPerRoomPerLevel as $level => $squaresPerRoom) {
            foreach ($squaresPerRoom as $squares) {
                // door on north
                usort($squares, function ($a, $b) {
                    return $a['y'] < $b['y'] ? -1 : 1;
                });
                $door = $squares[0];
                $this->door[$door['x']][$door['y']]['N'] = true;

                // door on west or east
                usort($squares, function ($a, $b) {
                    return $a['x'] < $b['x'] ? -1 : 1;
                });
                if (random_int(0, 1)) {
                    $door = $squares[0];
                    $this->door[$door['x']][$door['y']]['W'] = true;
                } else {
                    $door = $squares[count($squares) - 1];
                    $this->door[$door['x'] + 1][$door['y']]['W'] = true;
                }
            }
        }
    }

    public function groupByLevel(): array
    {
        $group = [];
        foreach ($this->grid as $x => $col) {
            foreach ($col as $y => $cell) {
                if ($cell !== 0) {
                    $group[$cell][] = ['x' => $x, 'y' => $y];
                }
            }
        }

        krsort($group);

        return $group;
    }

    public function groupSplitting(array $groupList): array
    {
        $roomPerLevel = [];
        foreach ($groupList as $level => $squareList) {
            $oneLevel = array_fill(0, $this->side, array_fill(0, $this->side, 0));
            foreach ($squareList as $square) {
                $oneLevel[$square['x']][$square['y']] = 1;
            }
            foreach ($this->levelSplitting($oneLevel) as $roomSquare) {
                $roomPerLevel[$level][] = $roomSquare;
            }
        }

        return $roomPerLevel;
    }

    public function levelSplitting(array $mapLevel): array
    {
        $roomList = [];
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                if ($mapLevel[$x][$y] === 0) {
                    continue;
                }

                $filler = new FloodFiller();
                $squareList = $filler->Scan($mapLevel, ['x' => $x, 'y' => $y]);
                // remove squares list of the room from the level
                foreach ($squareList as $square) {
                    $mapLevel[$square['x']][$square['y']] = 0;
                }
                $roomList[] = $squareList;
            }
        }

        return $roomList;
    }

}
