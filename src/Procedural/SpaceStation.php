<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

/**
 * Procedural generator of modular habitats (space station, tin can station, city block...)
 */
class SpaceStation extends GenericAutomaton
{

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
                    if (($neighbor > 0) && (rand(0, 100) < (25 * $neighbor))) {
                        $update[$x][$y] = 1;
                    }
                } else {
                    $neighbor = $this->neighborCount($x, $y);
                    if ($neighbor === 8) {
                        if (rand(0, 100) < 25) {
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

        echo '<g class="building">';
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
        echo '</g>' . PHP_EOL;
    }

    public function blurry(): void
    {
        $update = array_fill(0, $this->side, array_fill(0, $this->side, 0));

        for ($x = 1; $x < $this->side - 1; $x++) {
            for ($y = 1; $y < $this->side - 1; $y++) {
                $sum = 0;
                for ($dx = -1; $dx <= 1; $dx++) {
                    for ($dy = -1; $dy <= 1; $dy++) {
                        $sum += $this->grid[$x + $dx][$y + $dy];
                    }
                }
                $update[$x][$y] = (int) ceil($sum / 9);
            }
        }

        $this->grid = $update;
    }

    public function roomIterationDivide(float $divider): void
    {
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                if ($this->grid[$x][$y] > 0) {
                    $this->grid[$x][$y] = 1 + (int) ceil(($this->grid[$x][$y] - 1.0) / ($divider * 1.0));
                }
            }
        }
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

}
