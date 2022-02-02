<?php

require_once __DIR__ . '/../vendor/autoload.php';

$fac = new Trismegiste\MapGenerator\ModularHabitat();
$gen = $fac->createStreetGenerator(20, 3, 12, 3);
$gen->printSvg();
