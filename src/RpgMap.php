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
    protected $title = 'Battle Map';
    protected $parameter = [];

    public function __construct(CellularAutomaton $cell)
    {
        $this->cell = $cell;
    }

    public function setTitle(string $str): void
    {
        $this->title = $str;
    }

    public function setParameters(array $param): void
    {
        $this->parameter = $param;
    }

    public function appendLayer(SvgPrintable $layer): void
    {
        $this->layer[] = $layer;
    }

    public function printSvg(): void
    {
        $side = $this->cell->getSize();
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"800\" height=\"800\" viewBox=\"0 0 $side $side\">";
        echo "<title>{$this->title}</title>";
        echo "<desc><![CDATA[" . json_encode($this->parameter) . ']]></desc>';
        echo "<rect x=\"0\" y=\"0\" width=\"$side\" height=\"$side\" fill=\"white\"/>";

        $this->cell->printSvg();

        foreach ($this->layer as $layer) {
            $layer->printSvg();
        }

        echo '</svg>';
    }

}
