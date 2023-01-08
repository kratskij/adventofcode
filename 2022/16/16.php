<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->regex("Valve (\w+) has flow rate=(\d+); tunnels? leads? to valves? ([\w\s\,]+)");

$valvesState = $valvesRate = $tunnels = [];
foreach ($input as $k => $line) {
    list($valve, $rate, $connections) = $line;
    $connections = explode(", ", $connections);

    foreach ($connections as $connection) {
        $tunnels[$valve][$connection] = $connection;
#        $tunnels[$connection][$valve] = $valve;
    }
    if ($rate > 0) {
        $valvesState[$valve] = false;
        $valvesRate[$valve] = $rate;
    }
}


$q = [
    [
        "AA",
        "AA",
        $valvesState,
        26,
        0,
        []
    ]
];

$knownStates = [];

#2193; too low
#2202; too low
#2341; too low
#2363; ???
#2375; ???
#2455: CORRECT \o/

$filteredValves = array_filter($valvesRate);
arsort($filteredValves);
$valvesState = array_filter($valvesState, function($v, $k) use ($filteredValves) {
    return (isset($filteredValves[$k]));
}, ARRAY_FILTER_USE_BOTH);


$maxRate = array_sum($valvesRate);
$bestScore = -PHP_INT_MAX;
while ($state = array_shift($q)) {
    $newQ = [];
    list($mePos, $elPos, $valvesState, $minutes, $score, $trace) = $state;
    $rate = getRate($valvesState, $valvesRate);
    if ((count($q) % 1000) == 0) {
        echo count($q)." ($minutes minutes, bestScore: $bestScore, $rate of $maxRate)\n";
        $discarded = 0;
        foreach ($q as $r) {
            if (seen($r[0], $r[1], $r[2], $score)) {
                $discarded++;
            } else if (($r[4]+($r[3]+1)*$maxRate) < $bestScore) {
                $discarded++;
            } else {
                $newQ[] = $r;
            }
        }
        if ($discarded) {
            $q = $newQ;
        }
        usort($q, function($a, $b) use (&$valvesRate, $valvesState, $maxRate) {
            $aRate = $a[4]+$a[3]*$maxRate;
            $bRate = $b[4]+$b[3]*$maxRate;
            return $bRate - $aRate;
        });
    }

    if ($rate == $maxRate && $minutes > 0) {
        $score += $rate * $minutes;
        $minutes = 0;
    }

    if ($minutes == 0) {
        if ($score > $bestScore) {
            echo "\tnew best score: $score (" . implode(", ", $trace) . "\n";
            #echo count($q) . "($minutes)\n";
            $bestScore = $score;
            $p1 = $bestScore;
        }
        continue;
    }

    $score += $rate;
    if ($score + $maxRate*$minutes < $bestScore) {
        continue;
    }

    if (seen($mePos, $elPos, $valvesState, $score)) {
        continue;
    }

    // both are on the move
    moveBoth($q, $valvesState, $minutes, $mePos, $elPos, $tunnels, $score);

    for ($i = 0; $i < 2; $i++) {
        $onePos = ($i == 0) ? $mePos : $elPos;
        $otherPos = ($i == 0) ? $elPos : $mePos;
        if (isset($valvesState[$onePos]) && !$valvesState[$onePos]) {
            $valvesStateCopy = $valvesState;
            $valvesStateCopy[$onePos] = true;

            //one open valve; other is on the move:
            foreach ($tunnels[$otherPos] as $next) {
                $q[] = [$onePos, $next, $valvesStateCopy, $minutes-1, $score, array_merge($trace, ["$mePos:$elPos:op"])];
            }
        }
    }
    if ($mePos !== $elPos) {
        //both open valve:
        if (
            isset($valvesRate[$mePos]) && !$valvesState[$mePos] &&
            isset($valvesRate[$elPos]) && !$valvesState[$elPos]
        ) {
            $valvesStateCopy = $valvesState;
            $valvesStateCopy[$mePos] = true;
            $valvesStateCopy[$elPos] = true;
            $q[] = [$mePos, $elPos, $valvesStateCopy, $minutes-1, $score, array_merge($trace, ["$mePos:$elPos:op"])];
        }
    }

}

$p2 = $bestScore;

function seen($me, $el, $state, $score) {
    static $state;
    if ($state === null) {
        $state = [];
    }
    $s = 0;
    foreach ($state as $valve) {
        $s = ($s<<1) + (int)$valve;
    }
    $min = $min = min($me, $el);
    $max = $max = max($me, $el);
    if (isset($state[$s][$min][$max]) && $state[$s][$min][$max] > $score) {
        return true;
    }

    $state[$s][$min][$max] = $score;

    return false;
}

function moveBoth(&$q, $valves, $minutes, $mePos, $elPos, $tunnels, $score) {
    foreach ($tunnels[$mePos] as $meNext) {
        foreach ($tunnels[$elPos] as $elNext) {
            $q[] = [$meNext, $elNext, $valves, $minutes-1, $score, []];
        }
    }
}

function getRate($valves, &$rates) {
    $rate = 0;
    foreach ($valves as $id => $valve) {
        if ($valve) {
            $rate += $rates[$id];
        }
    }
    return $rate;
}

echo "P1: $p1\nP2: $p2\n";
