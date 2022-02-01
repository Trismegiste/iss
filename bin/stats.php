<?php

require_once __DIR__ . '/../vendor/autoload.php';

/*
 * MapGenerator
 */

$side = 100;
$gen = new \Trismegiste\MapGenerator\Procedural\SpaceStation($side);
$gen->set($side / 2, $side / 2, 1);
$countPerIterate = [];

for ($idx = 0; $idx < 100; $idx++) {
    $gen->iterate();
    $countPerIterate[$idx] = $gen->countPerlevel();
}

echo '<table>';
for ($row = 0; $row < 100; $row++) {
    echo '<tr>';
    echo "<th>$row</th>";
    for ($col = 1; $col < 50; $col++) {
        $cell = key_exists($col, $countPerIterate[$row]) ? $countPerIterate[$row][$col] : 0;
        echo "<td>$cell</td>";
    }
    echo '</tr>';
}
echo '</table>';
