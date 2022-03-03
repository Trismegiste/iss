<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

/**
 * Populates a grid with NPC
 */
class NpcPopulator implements \Trismegiste\MapGenerator\SvgPrintable
{

    const timeout = 0.1; // one tenth of second

    protected $npc;
    protected $side;
    protected $automat;

    public function __construct(CellularAutomaton $map)
    {
        $this->automat = $map;
        $this->side = $map->getSize();
        $this->npc = array_fill(0, $this->side, array_fill(0, $this->side, 0));
    }

    /**
     * generates NPC
     */
    public function generate(int $outsiderCount, int $insiderCount): void
    {
        $grid = $this->automat->getGrid();
        $outsider = $insider = 0;
        $deadline = microtime(true) + self::timeout;
        while ((microtime(true) < $deadline) && (($insider < $insiderCount) || ($outsider < $outsiderCount))) {
            $x = rand(0, $this->side - 1);
            $y = rand(0, $this->side - 1);
            if ($this->npc[$x][$y] === 0) {
                $cell = $grid[$x][$y];
                if ($cell === 0) {
                    if ($outsider < $outsiderCount) {
                        $this->npc[$x][$y] = 1;
                        $outsider++;
                    }
                } else {
                    if ($insider < $insiderCount) {
                        $this->npc[$x][$y] = 1;
                        $insider++;
                    }
                }
            }
        }
    }

    public function printSvg(): void
    {
        echo '<g fill="darkkhaki" id="token-layer">';
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                if ($this->npc[$x][$y]) {
                    echo "<circle cx=\"$x.5\" cy=\"$y.5\" r=\"0.2\"/>";
                }
            }
        }
        echo '</g>' . PHP_EOL;
    }

}
