<?php

require_once __DIR__ . '/vendor/autoload.php';

$side = 15;
$gen = new \Trismegiste\MapGenerator\SpaceStation($side);
$gen->set($side / 2, $side / 2, 1);

for ($idx = 0; $idx < 9; $idx++) {
    $gen->iterate();
}

$gen->roomIterationCapping(4);
$gen->findDoor();
$gen->dumpSvg();

$tmp = $gen->groupSplitting($gen->groupByLevel());

$dbg = print_r($tmp, true);
file_put_contents('yolo.txt', $dbg);
