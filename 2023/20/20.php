<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();

$input = $ir->regex("([a-zA-Z\%\&]+)\s\-\>\s([\w\s\,]+)");
$p1 = $p2 = 0;

const FLIPFLOP = "%";
const CONJUNCTION = "&";
$links = [];
$starts = [];
foreach ($input as $k => $line) {
    [$from, $to] = $line;
    $to = explode(", ", $to);
    if ($from == "broadcaster") {
        foreach ($to as $t) {
            $starts[] = [$t, $from, false];
        }
        continue;
    } else if (substr($from, 0, 1) == "%") {
        $from = substr($from, 1);
        $type = FLIPFLOP;
        $links[$from] = [$type, $to, false];
    } else if (substr($from, 0, 1) == CONJUNCTION) {
        $from = substr($from, 1);
        $type = CONJUNCTION;
        $links[$from] = [CONJUNCTION, $to, []];
    } else {
        die('wtf');
    }
}

foreach ($links as $name => $data) {
    [$type, $to, $state] = $data;
    foreach ($to as $toName) {
        $toData = $links[$toName] ?? false;
        if (!$toData) {
            continue;
        }
        [$toType, $toTo, $toState] = $toData;
        if ($toType == CONJUNCTION) {
            $links[$toName][2][$name] = false;
        }
    }
}

$pulseCount = ["low" => 0, "high" => 0];

$i = 0;
try {
    while(true) {
        $i++;
        $pulseCount["low"]++;
        button($links, $starts, $pulseCount);
        /*foreach ($links as $name => $l) {
            if ($l[0] == CONJUNCTION) {
                $values = [];
                foreach ($l[2] as $d) {
                    $values[] = $d ? "0" : "1";
                }
            } else {
                $values = [$l[2] ? "0" : "1"];
            }
            echo " $name:" . implode("", $values);
        }
        echo "\n";*/
        if ($i == 1000) {
            $p1 = array_product($pulseCount);
        }
        if (($i % 1000) == 0) {
            echo $i."\n";
        }
    }
} catch (\Exception $e) {
    $p2 = $i;
    echo $e->getMessage() . "\n";
}

function button(&$links, $q, &$pulses) {
    while ($curr = array_shift($q)) {
        [$name, $from, $pulse] = $curr;

        $pulses[$pulse ? "high" : "low"]++;
        if (!isset($links[$name])) {
            if (!$pulse) {
                throw new \Exception("Could not find $name, but low pulse received");
            }
            continue;
        }

        [$type, $to, $state] = $links[$name];
        switch ($type) {
            case FLIPFLOP:
                if (!$pulse) {
                    $state = !$state;
                    $links[$name] = [$type, $to, $state];

                    foreach ($to as $toName) {
                        $q[] = [$toName, $name, $state];
                    }
                }
                break;
            case CONJUNCTION:
                $state[$from] = $pulse;
                $links[$name] = [$type, $to, $state];
                $sendPulse = (count(array_filter($state)) != count($state));
                foreach ($to as $toName) {
                    $q[] = [$toName, $name, $sendPulse];
                }
                break;
        }
    }
}

echo "P1: $p1\nP2: $p2\n";
