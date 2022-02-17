<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\MapGenerator\Procedural\CellularAutomaton;
use Trismegiste\MapGenerator\RpgMap;

class RpgMapTest extends TestCase
{

    protected $sut;

    protected function setUp(): void
    {
        $cell = $this->createMock(CellularAutomaton::class);
        $this->sut = new RpgMap($cell);
    }

    public function testPrintSvg()
    {
        ob_start();
        $this->sut->printSvg();
        $str = ob_get_clean();

        $this->assertStringContainsString('<svg', $str);
        $this->assertStringEndsWith('</svg>', $str);
    }

    public function testParamMetadata()
    {
        $this->sut->setParameters(['yolo' => 123]);
        ob_start();
        $this->sut->printSvg();
        $str = ob_get_clean();

        $extracted = RpgMap::extractParameters($str);
        $this->assertEquals(['yolo' => 123], $extracted);
    }

}
