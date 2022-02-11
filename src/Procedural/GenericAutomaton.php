<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

use Trismegiste\MapGenerator\Utils\FloodFiller;

/**
 * An abstract cellular automaton
 */
abstract class GenericAutomaton implements CellularAutomaton
{

    protected $side;
    protected $grid;
    protected $cachedSquaresPerRoomPerLevel = null;

    public function __construct(int $side)
    {
        $this->side = $side;
        $this->grid = array_fill(0, $side, array_fill(0, $side, 0));
    }

    public function getGrid(): array
    {
        return $this->grid;
    }

    public function getSize(): int
    {
        return $this->side;
    }

    /**
     * Regroups squares by iteration level
     * @return array an array for each level of arrays of squares
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

    public function set(int $x, int $y, int $grp = 1): void
    {
        $this->grid[$x][$y] = $grp;
    }

    /**
     * Slices each level of iteration to generate a list of independant rooms
     * @param array $groupList
     * @return array an array for each level of arrays for each room of arrays of squares
     */
    public function splitEachLevelIntoRoom(array $groupList): array
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

    public function getSquaresPerRoomPerLevel(): array
    {
        if (is_null($this->cachedSquaresPerRoomPerLevel)) {
            $this->cachedSquaresPerRoomPerLevel = $this->splitEachLevelIntoRoom($this->groupByLevel());
        }

        return $this->cachedSquaresPerRoomPerLevel;
    }

}
