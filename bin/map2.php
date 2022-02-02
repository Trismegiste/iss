<?php

require_once __DIR__ . '/../vendor/autoload.php';

$fac = new Trismegiste\MapGenerator\ModularHabitat();
$fac->createDistrictGenerator(20, 6, 22, 4);
