<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

/**
 * Populates a grid with NPC
 */
class NpcPopulator
{

    protected $npc;
    protected $side;
    protected $npcCount;

    /**
     * @refactor inject the cellularAutomaton here, not in generate method
     */
    public function __construct(int $side, int $npcCount)
    {
        $this->side = $side;
        $this->npcCount = $npcCount;
        $this->npc = array_fill(0, $side, array_fill(0, $side, 0));
    }

    /**
     * @refactor specificy the number of NPC
     */
    public function generate(CellularAutomata $map): void
    {
        $grid = $map->getGrid();
        $cpt = 0;
        while ($cpt < $this->npcCount) {
            $x = random_int(0, $this->side - 1);
            $y = random_int(0, $this->side - 1);
            $cell = $grid[$x][$y];
            if (($cell === 0) && ($this->npc[$x][$y] === 0)) {
                $this->npc[$x][$y] = 1;
                $cpt++;
            }
        }
    }

    public function printSvg()
    {
        $width = $this->side;
        echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" width=\"800\" height=\"800\" viewBox=\"0 0 $width $width\">";
        echo '<g fill="green">';
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                if ($this->npc[$x][$y]) {
                    echo "<circle cx=\"$x.5\" cy=\"$y.5\" r=\"0.4\"/>";
                }
            }
        }
        echo '</g>';
        echo '</svg>';
    }

}
