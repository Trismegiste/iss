<?php

/*
 * MapGenerator
 */

use PHPUnit\Framework\TestCase;
use Trismegiste\MapGenerator\ModularHabitat;
use Trismegiste\MapGenerator\Procedural\SpaceStation;

class ModularHabitatTest extends TestCase
{

    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new ModularHabitat();
    }

    public function testCreateOneBlock()
    {
        ob_start();
        $this->sut->createOneBlockGenerator(25, 19, 4);
        $svg = ob_get_clean();
        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith('</svg>', $svg);
    }

    public function testCreateStreet()
    {
        ob_start();
        $this->sut->createStreetGenerator(20, 3, 12, 3);
        $svg = ob_get_clean();
        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith('</svg>', $svg);
    }

    public function testCreateDistrict()
    {
        ob_start();
        $this->sut->createDistrictGenerator(20, 3, 22, 4);
        $svg = ob_get_clean();
        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith('</svg>', $svg);
    }

}
