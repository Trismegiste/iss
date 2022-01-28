<?php

require_once __DIR__ . '/vendor/autoload.php';

$side = 25;
$gen = new \Trismegiste\MapGenerator\SpaceStation(25);
$gen->set($side / 2, $side / 2, 1);

for ($idx = 0; $idx < 19; $idx++) {
    $gen->iterate();
}

$gen->dumpSvg();
