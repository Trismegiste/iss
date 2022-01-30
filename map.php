<?php

require_once __DIR__ . '/vendor/autoload.php';

$side = 25;
$gen = new \Trismegiste\MapGenerator\SpaceStation($side);
$gen->set($side / 2, $side / 2, 1);

for ($idx = 0; $idx < 20; $idx++) {
    $gen->iterate();
}

$gen->roomIterationCapping(4);
$gen->findDoor();
$gen->dumpSvg();
