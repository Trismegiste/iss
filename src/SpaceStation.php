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

    public function dump(): void
    {
        foreach ($this->grid as $row) {
            foreach ($row as $cell) {
                echo dechex($cell);
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

    public function get(int $x, int $y): int
    {
        if (($x >= 0) && ($x < $this->side) && ($y >= 0) && ($y < $this->side)) {
            return $this->grid[$x][$y];
        }

        return 0;
    }

    public function neighborCount(int $x, int $y): int
    {
        $cpt = ($this->grid[$x][$y] > 0) ? -1 : 0;

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

    public function dumpSvg(): void
    {
        $scale = 32;
        $width = $this->side * $scale;
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"$width\" height=\"$width\">";

        foreach ($this->grid as $x => $col) {
            foreach ($col as $y => $cell) {
                $x0 = $scale * $x;
                $y0 = $scale * $y;
                $style = "stroke: black; stroke-width: 3";

                if ($cell !== $this->get($x, $y - 1)) {
                    $x2 = $x0 + $scale;
                    echo "<line x1=\"$x0\" y1=\"$y0\" x2=\"$x2\" y2=\"$y0\" style=\"$style\"/>";
                }

                if ($cell !== $this->get($x - 1, $y)) {
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
                if ($door & 1) {
                    $x1 = $x0 + $scale;
                    $y1 = $y0 + $scale / 3;
                    $y2 = $y0 + $scale * 2 / 3;
                    echo "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x1\" y2=\"$y2\" style=\"$style\"/>";
                }
                if ($door & 4) {
                    $x1 = $x0;
                    $y1 = $y0 + $scale / 3;
                    $y2 = $y0 + $scale * 2 / 3;
                    echo "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x1\" y2=\"$y2\" style=\"$style\"/>";
                }
                if ($door & 2) {
                    $x1 = $x0 + $scale / 3;
                    $x2 = $x0 + $scale * 2 / 3;
                    echo "<line x1=\"$x1\" y1=\"$y0\" x2=\"$x2\" y2=\"$y0\" style=\"$style\"/>";
                }
                if ($door & 8) {
                    $y1 = $y0 + $scale;
                    $x1 = $x0 + $scale / 3;
                    $x2 = $x0 + $scale * 2 / 3;
                    echo "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y1\" style=\"$style\"/>";
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
        $this->door = array_fill(0, $this->side, array_fill(0, $this->side, 0));

        foreach ($this->grid as $x => $col) {
            foreach ($col as $y => $cell) {
                if ($cell === 0) {
                    continue;
                }

                $xWalk = $x;
                $yWalk = $y;

                do {
                    $direction = random_int(0, 3);
                    switch ($direction) {
                        case 0: $xWalk++;
                            break;
                        case 1: $yWalk--;
                            break;
                        case 2: $xWalk--;
                            break;
                        case 3: $yWalk++;
                            break;
                    }

                    $currentGroup = $this->get($xWalk, $yWalk);
                    if ($currentGroup !== $cell) {
                        // we've just crossed a border
                        switch ($direction) {
                            case 0: $this->door[$xWalk - 1][$yWalk] |= 1;
                                break;
                            case 1: $this->door[$xWalk][$yWalk + 1] |= 2;
                                break;
                            case 2: $this->door[$xWalk + 1][$yWalk] |= 4;
                                break;
                            case 3: $this->door[$xWalk][$yWalk - 1] |= 8;
                                break;
                        }
                    }
                } while ($currentGroup === $cell);
            }
        }
    }

}
