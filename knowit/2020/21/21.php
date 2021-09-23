<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$queue = [];
$count = 0;
foreach ($input as $k => $line) {
    if ($line == "---") {
        usort($queue, "sortQueue");
        if (array_shift($queue)["name"] == "Claus") {
            die("$count\n");
        }
        $count++;
    } else {
        list($name, $priority) = explode(",", $line);
        $queue[] = [
            "priority" => $priority,
            "order" => $k,
            "name" => $name
        ];
    }
}

usort($queue, "sortQueue");
while (array_shift($queue)["name"] != "Claus") {
    $count++;
}

die("$count\n");

function sortQueue($a, $b)  {
    return ($a["priority"] == $b["priority"]) ? $a["order"] - $b["order"] : $a["priority"] - $b["priority"];
}



#557: CORRECT
