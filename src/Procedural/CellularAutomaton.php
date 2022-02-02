<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

/**
 * Contract for cellular automaton
 */
interface CellularAutomaton
{

    public function set(int $x, int $y, int $grp = 1): void;

    public function getGrid(): array;

    public function iterate(): void;

    public function printSvg(): void;

    public function getSize(): int;
}
