<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

/**
 * Fog of War
 */
class FogOfWar implements \Trismegiste\MapGenerator\SvgPrintable
{

    protected $automat;

    public function __construct(CellularAutomaton $map)
    {
        $this->automat = $map;
    }

    public function printSvg(): void
    {
        $width = $this->automat->getSize();
        $grid = $this->automat->getGrid();

        // fog on non-null
        echo '<g fill="black" id="fog-of-war">';
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $width; $y++) {
                $cell = $grid[$x][$y];
                if ($cell > 0) {
                    echo "<rect x=\"$x\" y=\"$y\" width=\"1\" height=\"1\"/>";
                }
            }
        }
        echo '</g>';
    }

}
