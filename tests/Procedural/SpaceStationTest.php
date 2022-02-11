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
        ob_start();
        $this->sut->printSvg();
        $svg = ob_get_clean();
        $this->assertStringStartsWith('<g', $svg);
        $this->assertStringEndsWith('</g>', $svg);
    }

    public function testGroupByLevel()
    {
        $this->sut->set(12, 12, 1);
        $this->sut->set(12, 13, 2);
        $grp = $this->sut->groupByLevel();
        $this->assertCount(2, $grp);
        $this->assertEquals(['x' => 12, 'y' => 12], $grp[1][0]);
        $this->assertEquals(['x' => 12, 'y' => 13], $grp[2][0]);

        return $grp;
    }

    /**
     * @depends testGroupByLevel
     */
    public function testSplitting(array $group)
    {
        $splitted = $this->sut->splitEachLevelIntoRoom($group);
        $this->assertCount(2, $splitted);
        $this->assertCount(1, $splitted[1][0]); // the first room on level 1 is one square
        $this->assertCount(1, $splitted[2][0]); // the first room on level 2 is one square
        $this->assertEquals(['x' => 12, 'y' => 12], $splitted[1][0][0]);
        $this->assertEquals(['x' => 12, 'y' => 13], $splitted[2][0][0]);
    }

}
