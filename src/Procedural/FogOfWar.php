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
        $group = $this->automat->getSquaresPerRoomPerLevel();

        // fog on non-null
        foreach ($group as $level => $roomList) {
            foreach ($roomList as $idx=>$room) {
                echo '<g fill="black" class="fog-of-war" data-level="' . $level . '">';
                echo "<title>room-$level-$idx</title>";
                foreach ($room as $square) {
                    $x = $square['x'];
                    $y = $square['y'];
                    echo "<rect x=\"$x\" y=\"$y\" width=\"1\" height=\"1\"/>";
                }
                echo '</g>';
            }
        }
    }

}
