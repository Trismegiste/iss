<?php

/*
 * MapGenerator
 */

use PHPUnit\Framework\TestCase;
use Tests\Trismegiste\MapGenerator\Fixtures\SampleGrid;
use Trismegiste\MapGenerator\Procedural\FogOfWar;

class FogOfWarTest extends TestCase
{

    protected $grid;
    protected $sut;

    protected function setUp(): void
    {
        $this->grid = new SampleGrid();
        $this->sut = new FogOfWar($this->grid);
    }

    public function testDoorsOneSquare()
    {
        $this->grid->set(12, 12, 1);
        ob_start();
        $this->sut->printSvg();
        $result = ob_get_clean();
        $this->assertStringStartsWith('<g', $result);
        $this->assertStringEndsWith('</g>', $result);
    }

}
