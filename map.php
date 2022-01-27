<?php

require_once __DIR__ . '/vendor/autoload.php';

$gen = new \Trismegiste\MapGenerator\SpaceStation(20);
$gen->set(10, 10, 1);

for ($idx = 0; $idx < 15; $idx++) {
    $gen->iterate();
}

$gen->dump();
