<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

use Trismegiste\MapGenerator\Utils\FloodFiller;

/**
 * Procedural generator of modular habitats (space station, tin can station, city block...)
 */
class SpaceStation implements CellularAutomaton
{

    protected $side;
    protected $grid;
    protected $door;

    public function __construct(int $side)
    {
        $this->side = $side;
        $this->grid = array_fill(0, $side, array_fill(0, $side, 0));
        $this->door = array_fill(0, $this->side, array_fill(0, $this->side, ['N' => false, 'W' => false]));
    }

    /**
     * Sets one square on the grid
     * @param int $x
     * @param int $y
     * @param int $grp The iteration count of the square (default : 1)
     */
    public function set(int $x, int $y, int $grp = 1): void
    {
        $this->grid[$x][$y] = $grp;
    }

    /**
     * Gets the grid content 
     * @return array An array of array : ($this->side)×($this->side) squares
     */
    public function getGrid(): array
    {
        return $this->grid;
    }

    /**
     * Gets the listing of doors
     * @return array An array of array : ($this->side)×($this->side) item containing an array ['N' => false|true, 'W' => false|true]
     */
    public function getDoors(): array
    {
        return $this->door;
    }

    /**
     * Runs an iteration of the generation
     */
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

    /**
     * Returns the count of non-zero neighbors. There are 8 neighbors around a square
     * WARNING : this function does not check whether the boundaries (x,y) are ok or not !
     * @param int $x
     * @param int $y
     * @return int
     */
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

    /**
     * Returns the count of non-zero neighbors placed on a cross. There are 4 neighbors around a square
     * WARNING : this function does not check whether the boundaries (x,y) are ok or not !
     * @param int $x
     * @param int $y
     * @return int
     */
    public function crossCount(int $x, int $y): int
    {
        $cpt = ($this->grid[$x + 1][$y] > 0) ? 1 : 0;
        $cpt += ($this->grid[$x - 1][$y] > 0) ? 1 : 0;
        $cpt += ($this->grid[$x][$y + 1] > 0) ? 1 : 0;
        $cpt += ($this->grid[$x][$y - 1] > 0) ? 1 : 0;

        return $cpt;
    }

    /**
     * Prints the SVG result on the standard stream
     */
    public function printSvg(): void
    {
        $width = $this->side;
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"800\" height=\"800\" viewBox=\"0 0 $width $width\">";
        $this->drawSquare(0, 0, $width, 'white');

        // Floors
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                $cell = $this->grid[$x][$y];
                if ($cell > 0) {
                    $grey = 95 - min($cell, 6) * 5;
                    $this->drawSquare($x, $y, 1, "hsl(0, 0%, $grey%)");
                }
            }
        }

        // Walls
        echo '<path style="stroke: black; stroke-width: 0.1" d="';
        for ($x = 1; $x < $this->side; $x++) {
            for ($y = 1; $y < $this->side; $y++) {
                $cell = $this->grid[$x][$y];

                if ($cell !== $this->grid[$x][$y - 1]) {
                    echo " M $x $y h 1";
                }

                if ($cell !== $this->grid[$x - 1][$y]) {
                    echo " M $x $y v 1";
                }
            }
        }
        echo '"/>';

        // Doors
        echo '<path style="stroke: red; stroke-width: 0.15" d="';
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                $door = $this->door[$x][$y];
                if ($door['W']) {
                    echo " M $x $y.25 v 0.5";
                }
                if ($door['N']) {
                    echo " M $x.25 $y h 0.5";
                }
            }
        }
        echo '"/>';

        echo '</svg>';
    }

    /**
     * Print a square with a color of SVG
     */
    protected function drawSquare(float $x, float $y, float $size, string $color): void
    {
        echo "<rect x=\"$x\" y=\"$y\" width=\"$size\" height=\"$size\" fill=\"$color\"/>";
    }

    /**
     * Print a line of SVG
     */
    protected function drawLine(float $x1, float $y1, float $x2, float $y2, string $style): void
    {
        echo "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" style=\"$style\"/>";
    }

    /**
     * Iterates on each square and caps the level of iteration
     * @param int $threshold All square above $threshold are capped at $threshold
     */
    public function roomIterationCapping(int $threshold): void
    {
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                $cell = $this->grid[$x][$y];
                if ($cell > $threshold) {
                    $this->grid[$x][$y] = $threshold;
                }
            }
        }
    }

    /**
     * Scans the grids and returns the x & y boundaries of a non-zero subset of the grid
     * @return array
     */
    public function getMinMax(): array
    {
        $xmin = $ymin = $this->side;
        $xmax = $ymax = 0;

        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                if ($this->grid[$x][$y] > 0) {
                    if ($xmax < $x) {
                        $xmax = $x;
                    }
                    if ($xmin > $x) {
                        $xmin = $x;
                    }
                    if ($ymax < $y) {
                        $ymax = $y;
                    }
                    if ($ymin > $y) {
                        $ymin = $y;
                    }
                }
            }
        }

        return ['xmin' => $xmin, 'xmax' => $xmax, 'ymin' => $ymin, 'ymax' => $ymax];
    }

    /**
     * Generates doors of the current grid
     */
    public function findDoor(): void
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

    /**
     * Slices the grid by level of iteration
     * @return array A list by level of A list of [x,y] for each level
     */
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

    /**
     * Slices a level of iteration to generate a list of independant rooms
     * @param array $groupList
     * @return array
     */
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

    /**
     * Splits one level of iteration into a list of rooms.
     * A room is a list of squares
     * @param array $mapLevel
     * @return array
     */
    protected function levelSplitting(array $mapLevel): array
    {
        $roomList = [];
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                if ($mapLevel[$x][$y] === 0) {
                    continue;
                }

                $filler = new FloodFiller();
                $squareList = $filler->Scan($mapLevel, ['x' => $x, 'y' => $y]);
                // remove squares list of the room from the current level
                foreach ($squareList as $square) {
                    $mapLevel[$square['x']][$square['y']] = 0;
                }
                $roomList[] = $squareList;
            }
        }

        return $roomList;
    }

    public function countPerlevel(): array
    {
        $counter = [];
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                $cell = $this->grid[$x][$y];
                if (!key_exists($cell, $counter)) {
                    $counter[$cell] = 0;
                }
                $counter[$cell]++;
            }
        }

        return $counter;
    }

    public function getSize(): int
    {
        return $this->side;
    }

}
