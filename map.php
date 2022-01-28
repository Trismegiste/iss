<?php

require_once __DIR__ . '/vendor/autoload.php';

$side = 25;
$gen = new \Trismegiste\MapGenerator\SpaceStation(25);
$gen->set($side / 2, $side / 2, 1);

for ($idx = 0; $idx < 19; $idx++) {
    $gen->iterate();
}
$gen->roomIterationCapping(4);
$tmp = $gen->groupByLevel();
$gen->findDoor();
$gen->dumpSvg();
die();
echo PHP_EOL;
foreach ($tmp as $k => $level) {
    echo $k . ' = ' . count($level) . PHP_EOL;
}
