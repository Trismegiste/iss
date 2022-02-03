<?php

/*
 * MapGenerator
 */

use Tests\Trismegiste\MapGenerator\Fixtures\SampleGrid;
use Trismegiste\MapGenerator\Procedural\DoorLayer;

class DoorLayerTest extends \PHPUnit\Framework\TestCase
{

    protected $grid;
    protected $sut;

    protected function setUp(): void
    {
        $this->grid = new SampleGrid();
        $this->sut = new DoorLayer($this->grid);
    }

    public function testDoorsOneSquare()
    {
        $this->grid->set(12, 12, 1);
        $this->sut->findDoor();
        $doors = $this->sut->getDoors();
        $this->assertTrue($doors[12][12]['N']);
    }

    public function testDoorsOVerticalHallway()
    {
        $this->grid->set(12, 11, 1);
        $this->grid->set(12, 12, 1);
        $this->grid->set(12, 13, 1);
        $this->sut->findDoor();
        $doors = $this->sut->getDoors();
        $this->assertTrue($doors[12][11]['N']);
    }

    public function testDoorsOHorizontalHallway()
    {
        $this->grid->set(11, 12, 1);
        $this->grid->set(12, 12, 1);
        $this->grid->set(13, 12, 1);
        $this->sut->findDoor();
        $doors = $this->sut->getDoors();
        $this->assertTrue($doors[11][12]['W'] xor $doors[14][12]['W']);
    }

}
