<?php

/*
 * MapGenerator
 */

namespace Trismegiste\MapGenerator\Procedural;

/**
 * Cotract for cellular automata
 */
interface CellularAutomata
{

    public function set(int $x, int $y, int $grp = 1): void;

    public function getGrid(): array;

    public function iterate(): void;

    public function dumpSvg(): void;
}
