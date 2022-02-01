<?php

require_once __DIR__ . '/../vendor/autoload.php';

/*
 * MapGenerator
 */

$side = 100;
$countPerIterate = array_fill(0, 100, array_fill(0, 100, 0));

for ($cumul = 0; $cumul < 40; $cumul++) {
    $gen = new \Trismegiste\MapGenerator\Procedural\SpaceStation($side);
    $gen->set($side / 2, $side / 2, 1);

    for ($idx = 0; $idx < 100; $idx++) {
        $gen->iterate();
        foreach ($gen->countPerlevel() as $level => $cnt) {
            $countPerIterate[$idx][$level] += $cnt;
        }
    }
}

echo '<table>';
for ($row = 0; $row < 100; $row++) {
    echo '<tr>';
    echo "<th>$row</th>";
    for ($col = 1; $col < 50; $col++) {
        $cell = $countPerIterate[$row][$col]/40;
        echo "<td>$cell</td>";
    }
    echo '</tr>';
}
echo '</table>';
