<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->csv(" ");

$links = [];

foreach ($input as $k => $line) {
    list($parent, $child) = $line;
    if (!isset($links[$parent])) {
        $links[$parent] = [
            "children" => [],
            "parents" => [],
            "assembling" => false
        ];
    }
    if (!isset($links[$child])) {
        $links[$child] = [
            "children" => [],
            "parents" => [],
            "assembling" => false
        ];
    }
    $links[$parent]["children"][$child] = true;
    $links[$child]["parents"][$parent] = true;
}
$origLinks = $links;

$str = "";
$tasks = findAvailableTasks($links);
while ($task = reset($tasks)) {
    foreach(array_keys($links[$task]["children"]) as $c) {
        unset($links[$c]["parents"][$task]);
    }
    $str .= $task;
    unset($links[$task]);
    $tasks = findAvailableTasks($links);
}
echo "Part 1: $str\n";

$workerCount = ($test) ? 2 : 5;
$workers = [];
for ($i = 0; $i < $workerCount; $i++) {
    $workers[] = ["timeLeft" => 1, "task" => false];
}

$time = -1;
$initTime = 60;
while (true) { #!empty($tasks) || array_sum(array_column($workers, "timeLeft"))) {
    $time++;
    $availableWorkers = [];
    foreach ($workers as $i => $worker) {
        $workers[$i]["timeLeft"]--;
        if ($workers[$i]["timeLeft"] <= 0) {
            $availableWorkers[] = $i;
            if ($worker["task"]) {
                foreach ($origLinks[$worker["task"]]["children"] as $id => $child) {
                    unset($origLinks[$id]["parents"][$worker["task"]]);
                }
            }
        }
    }
    #echo "$time " . implode("  ", array_column($workers, "task")) . "\n";

    if (empty($availableWorkers)) {
        continue;
    }

    $tasks = findAvailableTasks($origLinks);

    if (empty($tasks) && count($availableWorkers) == count($workers)) {
        break;
    }

    while (!empty($availableWorkers) && !empty($tasks)) {
        $nextId = array_shift($tasks);
        $worker = array_shift($availableWorkers);
        $origLinks[$nextId]["assembling"] = true;
        $workers[$worker]["task"] = $nextId;
        $workers[$worker]["timeLeft"] = ord($nextId) - 64 + $initTime;
    }
}
echo "Part 2: $time\n";

function findAvailableTasks($links) {
    $tasks = [];
    foreach ($links as $id => $l) {
        if (!$l["assembling"] && empty($l["parents"])) {
            $tasks[$id] = $id;
        }
    }
    ksort($tasks);
    return $tasks;
}
