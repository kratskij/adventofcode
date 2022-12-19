<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("Blueprint (\d+): Each ore robot costs (\d+) ore. Each clay robot costs (\d+) ore. Each obsidian robot costs (\d+) ore and (\d+) clay. Each geode robot costs (\d+) ore and (\d+) obsidian.");

const ORE = "ore";
const CLAY = "clay";
const OBSIDIAN = "obsidian";
const GEODE = "geode";

$p1 = 0;
$p2 = 1;

foreach ($input as $k => $line) {
    list($blueprint, $oreOre, $clayOre, $obsidianOre, $obsidianClay, $geodeOre, $geodeObsidian) = array_map("intval", $line);

    $prices = [
        ORE => [ORE => $oreOre],
        CLAY => [ORE => $clayOre],
        OBSIDIAN => [ORE => $obsidianOre, CLAY => $obsidianClay],
        GEODE => [ORE => $geodeOre, OBSIDIAN => $geodeObsidian],
    ];

    $orePrices = [];
    foreach ($prices as $pricek => $price) {
        foreach ($price as $pricek2 => $price2) {
            if ($pricek2 == ORE) {
                $orePrices[$pricek] = $price2;
            } else {
                $orePrices[$pricek] += $price2 * $orePrices[$pricek2];
            }
        }
    }

    $blueprint = new Blueprint($prices, $orePrices);

    $p1 += ($k+1) * $blueprint->stopAt(24);

    if ($k < 3) {
        $p2 *= $blueprint->stopAt(32);
    }

    unset($blueprint);
}
echo "P1: $p1\nP2: $p2\n";

class Blueprint {
    private $_seen = [];
    private $_prices;
    private $_orePrices;
    private $_stopAtMinute;
    private $_states = [];

    public function __construct($prices, $orePrices) {
        $this->_prices = $prices;
        $this->_orePrices = $orePrices;
    }

    public function stopAt($stopAtMinute) {
        $this->_stopAtMinute = $stopAtMinute;
        if (!empty($this->_states)) {
            $max = -PHP_INT_MAX;
            foreach ($this->_states as $state) {
                list($robots, $inventoryBefore, $minutes) = $state;
                $max = max($max, $this->maxGeodes($robots, $inventoryBefore, $minutes));
            }
            return $max;

        } else {
            return $this->maxGeodes(
                [ORE => 1, CLAY => 0, OBSIDIAN => 0, GEODE => 0],
                [ORE => 0, CLAY => 0, OBSIDIAN => 0, GEODE => 0],
                0
            );
        }
    }

    public function maxGeodes($robots, $inventoryBefore, $minutes = 0) {
        if ($minutes == $this->_stopAtMinute) {
            if ($inventoryBefore[GEODE] > 0) {
                $this->_states[] = [$robots, $inventoryBefore, $minutes];
            }

            return $inventoryBefore[GEODE];
        }

        $q = [];

        $inventoryAfter = $inventoryBefore;
        foreach ($robots as $type => $robotCount) {
            $inventoryAfter[$type] += $robotCount;
        }

        foreach ($this->_prices as $robotType => $price) {
            $hasEnough = true;
            $inventoryBefore2 = $inventoryBefore;
            $robots2 = $robots;
            foreach ($price as $priceType => $p) {
                if ($inventoryBefore2[$priceType] < $p) {
                    $hasEnough = false;
                    break;
                }
            }
            if ($hasEnough) {
                $robots2[$robotType]++;
                $inventoryAfter2 = $inventoryAfter;
                foreach ($this->_prices[$robotType] as $priceType => $p) {
                    $inventoryAfter2[$priceType] -= $p;
                    $inventoryBefore2[$priceType] -= $p;
                }
                $this->add($q, $robots2, $inventoryAfter2, $minutes);
            }
        }

        $this->add($q, $robots, $inventoryAfter, $minutes);

        $maxGeodes = -PHP_INT_MAX;
        foreach ($q as $state) {
            $maxGeodes = max($maxGeodes, $this->maxGeodes($state[0], $state[1], $minutes+1));
        }

        return $maxGeodes;
    }

    public function add(&$q, $robots, $inventory, $minutes) {
        $idx = implode("_", $robots)."_".$minutes;
        $score = (
            ($this->_stopAtMinute-$minutes) * $inventory[GEODE] * $this->_orePrices[GEODE] +
            ($this->_stopAtMinute-$minutes) * $inventory[OBSIDIAN] * $this->_orePrices[OBSIDIAN] +
            ($this->_stopAtMinute-$minutes) * $inventory[CLAY] * $this->_orePrices[CLAY] +
            ($this->_stopAtMinute-$minutes) * $inventory[ORE] * $this->_orePrices[ORE]
        );

        if (!isset($this->_seen[$idx]) || $this->_seen[$idx] <= $score) {
            $q[] = [$robots, $inventory];
            $this->_seen[$idx] = $score;
        }
    }
}
