<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->csv("🎄");
$p1 = $p2 = false;

$links = [];
$ends = [];
foreach ($input as $k => $line) {
    $links["SANTA"][reset($line)] = reset($line);

    $child = array_pop($line);
    $ends[$child] = $child;
    while ($parent = array_pop($line)) {
        $links[$parent][$child] = $child;
        $child = $parent;
    }
}

#var_dump("AFTER INPUT", $links);
$change = true;
while ($change) {
    $change = false;
    foreach ($links as $parent => $children) {
        foreach ($children as $child) {
            if (!isset($ends[$child])) {
                $change = true;
                #echo "$child does not exist\n";
                foreach ($links[$child] as $newChild) {
                    #echo "\tadding $newChild as child of $parent\n";
                    $links[$parent][$newChild] = $newChild;
                }
                unset($links[$child]);
                foreach ($links as $parent2 => $children) {
                    if (isset($children[$child])) {
                        unset($links[$parent2][$child]);
                    }
                }
            }
        }
    }

    #var_dump("AFTER REMOVING RETIREDS", $links);

    foreach ($links as $parent => $children) {
        if (count($children) == 1) {
            $newChild = reset($children);
            if (isset($links[$newChild])) {
                $change = true;
                foreach ($links as $newParent => $newParentChildren) {
                    if (isset($newParentChildren[$parent])) {
                        break;
                    }
                }
                #echo "firing $parent, adding $newChild as child of $newParent\n";
                unset($links[$newParent][$parent]);
                unset($links[$parent]);
                $links[$newParent][$newChild] = $newChild;

                unset($ends[$parent]);
            }
        }
    }
}

#var_dump("AFTER REMOVING UNNECESSARIES", $links);

$workerElves = $mellomlederElves = [];
foreach ($links as $parent => $children) {
    foreach ($children as $child) {
        if (isset($links[$child])) {
            $mellomlederElves[$child] = $child;
        } else {
            $workerElves[$child] = $child;
        }
    }
}

var_Dump("END CALCULATIONS", $workerElves, $mellomlederElves);
echo count($workerElves) . " - " . count($mellomlederElves) . " = " . (count($workerElves) - count($mellomlederElves)) . "\n";
