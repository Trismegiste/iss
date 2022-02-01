<?php

require_once __DIR__ . '/../vendor/autoload.php';

$fac = new Trismegiste\MapGenerator\ModularHabitat();
$gen = $fac->createOneBlockGenerator(25, 19, 4);
//$gen->dumpSvg();

$npc = new Trismegiste\MapGenerator\Procedural\NpcPopulator(25, 20);
$npc->generate($gen);
$npc->printSvg();