<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

/**
 * Contract for cellular automaton
 */
interface CellularAutomaton extends \Trismegiste\MapGenerator\SvgPrintable
{

    /**
     * Sets the level (or group) of one square
     * @param int $x
     * @param int $y
     * @param int $grp
     */
    public function set(int $x, int $y, int $grp = 1): void;

    /**
     * Gets the grid content
     * @return array
     */
    public function getGrid(): array;

    /**
     * Makes one iteration
     */
    public function iterate(): void;

    /**
     * Gets the suare grid size
     * @return int
     */
    public function getSize(): int;

    /**
     * Slices the grid by level of iteration
     * @return array A list by level of A list of [x,y] for each level
     */
    public function groupByLevel(): array;
}
