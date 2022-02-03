<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

use Trismegiste\MapGenerator\SvgPrintable;
use Trismegiste\MapGenerator\Utils\FloodFiller;

/**
 * Layer for doors
 */
class DoorLayer implements SvgPrintable
{

    protected $automat;
    protected $door;
    protected $side;

    public function __construct(CellularAutomaton $map)
    {
        $this->automat = $map;
        $this->side = $map->getSize();
        $this->door = array_fill(0, $map->getSize(), array_fill(0, $map->getSize(), ['N' => false, 'W' => false]));
    }

    public function printSvg(): void
    {
        // Doors
        echo '<path style="stroke: red; stroke-width: 0.15" d="';
        for ($x = 0; $x < $this->side; $x++) {
            for ($y = 0; $y < $this->side; $y++) {
                $door = $this->door[$x][$y];
                if ($door['W']) {
                    echo " M $x $y.25 v 0.5";
                }
                if ($door['N']) {
                    echo " M $x.25 $y h 0.5";
                }
            }
        }
        echo '"/>';
    }

    /**
     * Gets the listing of doors
     * @return array An array of array : ($this->side)×($this->side) item containing an array ['N' => false|true, 'W' => false|true]
     */
    public function getDoors(): array
    {
        return $this->door;
    }

    /**
     * Generates doors of the current grid
     */
    public function findDoor(): void
    {
        $squaresPerRoomPerLevel = $this->groupSplitting($this->automat->groupByLevel());

        foreach ($squaresPerRoomPerLevel as $level => $squaresPerRoom) {
            foreach ($squaresPerRoom as $squares) {
                // door on north
                usort($squares, function ($a, $b) {
                    return $a['y'] < $b['y'] ? -1 : 1;
                });
                $door = $squares[0];
                $this->door[$door['x']][$door['y']]['N'] = true;

                // door on west or east
                usort($squares, function ($a, $b) {
                    return $a['x'] < $b['x'] ? -1 : 1;
                });
                if (rand(0, 1)) {
                    $door = $squares[0];
                    $this->door[$door['x']][$door['y']]['W'] = true;
                } else {
                    $door = $squares[count($squares) - 1];
                    $this->door[$door['x'] + 1][$door['y']]['W'] = true;
                }
            }
        }
    }

    /**
     * Slices a level of iteration to generate a list of independant rooms
     * @param array $groupList
     * @return array
     */
    public function groupSplitting(array $groupList): array
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

}
