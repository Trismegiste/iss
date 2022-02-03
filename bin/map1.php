<?php

require_once __DIR__ . '/../vendor/autoload.php';

$fac = new Trismegiste\MapGenerator\ModularHabitat();
$fac->createOneBlockGenerator(50, 40, 15);
