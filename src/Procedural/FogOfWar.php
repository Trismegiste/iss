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
        $group = $this->automat->groupByLevel();

        // fog on non-null
        foreach ($group as $level => $listing) {
            echo '<g fill="black" class="fog-of-war" data-level="' . $level . '">';
            foreach ($listing as $square) {
                $x = $square['x'];
                $y = $square['y'];
                echo "<rect x=\"$x\" y=\"$y\" width=\"1\" height=\"1\"/>";
            }
            echo '</g>';
        }
    }

}
