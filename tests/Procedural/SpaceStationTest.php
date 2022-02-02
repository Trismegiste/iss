<?php

/*
 * MapGenerator
 */

use PHPUnit\Framework\TestCase;
use Trismegiste\MapGenerator\Procedural\SpaceStation;

class SpaceStationTest extends TestCase
{

    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new SpaceStation(25);
    }

    public function testEmpty()
    {
        $grid = $this->sut->getGrid();
        $this->assertCount(25, $grid);
        $this->assertCount(25, $grid[0]);
        $this->assertCount(25, $grid[24]);
        $this->assertCount(25, $this->sut->getDoors());
    }

    public function testSetter()
    {
        $this->sut->set(7, 17, 1);
        $grid = $this->sut->getGrid();
        $this->assertEquals(1, $grid[7][17]);
    }

    public function testEmptyIterate()
    {
        $this->sut->iterate();
        $grid = $this->sut->getGrid();
        $cpt = 0;
        for ($x = 0; $x < 25; $x++) {
            for ($y = 0; $y < 25; $y++) {
                $cpt += $grid[$x][$y];
            }
        }
        $this->assertEquals(0, $cpt);
    }

    public function testSomeIterations()
    {
        $this->sut->set(12, 12, 1);
        for ($idx = 0; $idx < 12; $idx++) {
            $this->sut->iterate();
        }

        $borne = $this->sut->getMinMax();
        $this->assertGreaterThan(12, $borne['xmax']);
        $this->assertLessThan(12, $borne['xmin']);
        $this->assertGreaterThan(12, $borne['ymax']);
        $this->assertLessThan(12, $borne['ymin']);
    }

    public function testDoorsOneSquare()
    {
        $this->sut->set(12, 12, 1);
        $this->sut->findDoor();
        $doors = $this->sut->getDoors();
        $this->assertTrue($doors[12][12]['N']);
    }

    public function testDoorsOVerticalHallway()
    {
        $this->sut->set(12, 11, 1);
        $this->sut->set(12, 12, 1);
        $this->sut->set(12, 13, 1);
        $this->sut->findDoor();
        $doors = $this->sut->getDoors();
        $this->assertTrue($doors[12][11]['N']);
    }

    public function testDoorsOHorizontalHallway()
    {
        $this->sut->set(11, 12, 1);
        $this->sut->set(12, 12, 1);
        $this->sut->set(13, 12, 1);
        $this->sut->findDoor();
        $doors = $this->sut->getDoors();
        $this->assertTrue($doors[11][12]['W'] xor $doors[14][12]['W']);
    }

    public function testCapping()
    {
        $this->sut->set(12, 12, 1);
        for ($idx = 0; $idx < 12; $idx++) {
            $this->sut->iterate();
        }
        $this->sut->roomIterationCapping(1);
        $equalOne = true;
        $grid = $this->sut->getGrid();
        for ($x = 0; $x < 25; $x++) {
            for ($y = 0; $y < 25; $y++) {
                $cell = $grid[$x][$y];
                if ($cell > 0) {
                    $equalOne = $equalOne && ($cell === 1);
                }
            }
        }
        $this->assertTrue($equalOne);
    }

    public function testSvgGeneration()
    {
        $this->sut->set(12, 12, 1);
        for ($idx = 0; $idx < 15; $idx++) {
            $this->sut->iterate();
        } $this->sut->roomIterationCapping(4);
        $this->sut->findDoor();
        ob_start();
        $this->sut->printSvg();
        $svg = ob_get_clean();
        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith('</svg>', $svg);
    }

}
