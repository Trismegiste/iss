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
    public function generate(int $npcCount): void
    {
        $grid = $this->automat->getGrid();
        $cpt = 0;
        while ($cpt < $npcCount) {
            $x = random_int(0, $this->side - 1);
            $y = random_int(0, $this->side - 1);
            $cell = $grid[$x][$y];
            if (($cell === 0) && ($this->npc[$x][$y] === 0)) {
                $this->npc[$x][$y] = 1;
                $cpt++;
            }
        }
    }

    public function printSvg(): void
    {
        echo '<g fill="green">';
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                if ($this->npc[$x][$y]) {
                    echo "<circle cx=\"$x.5\" cy=\"$y.5\" r=\"0.4\"/>";
                }
            }
        }
        echo '</g>';
    }

}
