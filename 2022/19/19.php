<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->regex("Blueprint (\d+): Each ore robot costs (\d+) ore. Each clay robot costs (\d+) ore. Each obsidian robot costs (\d+) ore and (\d+) clay. Each geode robot costs (\d+) ore and (\d+) obsidian.");

$minutes = 24;

const ORE = "ore";
const CLAY = "clay";
const OBSIDIAN = "obsidian";
const GEODE = "geode";

$p1 = 0;

foreach ($input as $k => $line) {
    list($blueprint, $oreOre, $clayOre, $obsidianOre, $obsidianClay, $geodeOre, $geodeObsidian) = array_map("intval", $line);

    $prices = [
        GEODE => [ORE => $geodeOre, OBSIDIAN => $obsidianClay],
        OBSIDIAN => [ORE => $obsidianOre, CLAY => $obsidianClay],
        CLAY => [ORE => $clayOre],
        ORE => [ORE => $oreOre],
    ];


    $maxGeodes = maxGeodes(
        $prices,
        [ORE => 1, CLAY => 0, OBSIDIAN => 0, GEODE => 0],
        [ORE => 0, CLAY => 0, OBSIDIAN => 0, GEODE => 0],
        24
    );
    echo "blueprint " . ($k+1) . "has max $maxGeodes geodes\n";
    $p1 += $maxGeodes * ($k+1);
}

echo "P1: $p1\nP2: $p2\n";

function maxGeodes(&$prices, $robots, $inventory, $minutes) {
    #$minutes--;
    #echo "\tinventory: " . json_encode($inventory) . "\n\trobots: " . json_encode($robots)."\n";
    #echo "minute $minutes\n";

    if ($minutes == 0) {
        return $inventory[GEODE];
    }

    $q = [];
    foreach ($prices as $robotType => $price) {
        $hasEnough = true;
        foreach ($price as $priceType => $p) {
            if ($inventory[$priceType] < $p) {
                $hasEnough = false;
                break;
            }
        }
        if ($hasEnough) {
            $robotsCopy = $robots;
            $inventoryCopy = $inventory;
            $robotsCopy[$robotType]++;
            foreach ($prices[$robotType] as $priceType => $p) {
                $inventoryCopy[$priceType] -= $p;
            }
            foreach ($robots as $type => $robotCount) {
                $inventoryCopy[$type] += $robotCount;
            }
            add($q, $robotsCopy, $inventoryCopy, $minutes-1);
         }
    }
    // adds "buy nothing"
    foreach ($robots as $type => $robotCount) {
        $inventory[$type] += $robotCount;
    }
    add($q, $robots, $inventory, $minutes-1);

    $maxGeodes = -PHP_INT_MAX;
    foreach ($q as $state) {
        #echo "\tchecking a state" . json_encode($state) . "\n";
        $maxGeodes = max($maxGeodes, maxGeodes($prices, $state[0], $state[1], $minutes-1));
    }
    if ($maxGeodes > 0) {
        echo "\treturning $maxGeodes\n";
    }

    return $maxGeodes;
}

function add(&$q, $robots, $inventory, $minutes) {
    static $seen;
    if ($seen === null) {
        $seen = [];
    }

    $idx = implode("_", $robots)."_"."_".($minutes);
    $score = $minutes * (
        $inventory[GEODE] * 1000000000 +
        $inventory[OBSIDIAN] * 1000000 +
        $inventory[CLAY] * 1000 +
        $inventory[ORE]
    );
    if (!isset($seen[$idx])) {
        #echo "$minutes ";
        #echo "minute $minutes\n\tAdding!\n\tinventory: " . json_encode($inventory) . "\n\trobots: " . json_encode($robots)."\n";
        $q[] = [$robots, $inventory];
        $seen[$idx] = $score;
    } else if ($seen[$idx] < $score) {
        $q[] = [$robots, $inventory];
        $seen[$idx] = $score;
    }
}
