<?php

require_once __DIR__ . '/../vendor/autoload.php';

$fac = new Trismegiste\MapGenerator\ModularHabitat();
$fac->createOneBlockGenerator(25, 19, 4);
