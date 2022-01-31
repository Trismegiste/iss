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
        $proc = $this->sut->createOneBlockGenerator(25, 19, 4);
        $this->assertInstanceOf(SpaceStation::class, $proc);
        $this->assertCount(25, $proc->getGrid());
    }

    public function testCreateStreet()
    {
        $proc = $this->sut->createStreetGenerator(20, 3, 12, 3);
        $this->assertInstanceOf(SpaceStation::class, $proc);
        $this->assertCount(60, $proc->getGrid());
    }

    public function testCreateDistrict()
    {
        $proc = $this->sut->createDistrictGenerator(20, 3, 22, 4);
        $this->assertInstanceOf(SpaceStation::class, $proc);
        $this->assertCount(60, $proc->getGrid());
    }

}
