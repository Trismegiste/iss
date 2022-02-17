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

    public function testMetadata()
    {
        $this->sut->setTitle('Free sample');
        $this->sut->setParameters(['yolo' => 123]);
        ob_start();
        $this->sut->printSvg();
        $str = ob_get_clean();

        $extracted = RpgMap::extractMetadata($str);
        $this->assertInstanceOf(\stdClass::class, $extracted);
        $this->assertObjectHasAttribute('tagTitle', $extracted);
        $this->assertObjectHasAttribute('tagDesc', $extracted);
        $this->assertEquals('Free sample', $extracted->tagTitle);
        $this->assertEquals(['yolo' => 123], $extracted->tagDesc);
    }

}
