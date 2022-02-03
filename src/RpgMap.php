<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator;

use Trismegiste\MapGenerator\Procedural\CellularAutomaton;

/**
 * A map for RPG
 */
class RpgMap implements SvgPrintable
{

    protected $cell;
    protected $layer = [];

    public function __construct(CellularAutomaton $cell)
    {
        $this->cell = $cell;
    }

    public function appendLayer(SvgPrintable $layer): void
    {
        $this->layer[] = $layer;
    }

    public function printSvg(): void
    {
        $side = $this->cell->getSize();
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"800\" height=\"800\" viewBox=\"0 0 $side $side\">";
        echo "<rect x=\"0\" y=\"0\" width=\"$side\" height=\"$side\" fill=\"white\"/>";

        $this->cell->printSvg();

        foreach ($this->layer as $layer) {
            $layer->printSvg();
        }

        echo '</svg>';
    }

}
