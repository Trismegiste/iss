<?php

/*
 * MapGenerator
 */

namespace Tests\Trismegiste\MapGenerator\Fixtures;

/**
 * Fixtures for Tests
 */
class SampleGrid extends \Trismegiste\MapGenerator\Procedural\GenericAutomaton
{

    public function __construct()
    {
        parent::__construct(25);
    }

    public function iterate(): void
    {
        
    }

    public function printSvg(): void
    {
        
    }

}
