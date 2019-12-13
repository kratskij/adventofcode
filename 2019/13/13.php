<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__ . '/ArcadeCabinet.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");
#$input = array_map("intval", $input);

$arcadeCabinet = new ArcadeCabinet($code);
$arcadeCabinet->render();
echo "Rendered!\n";
$arcadeCabinet->printGrid();

$arcadeCabinet->cheat();
$gameStack = [$arcadeCabinet];
while ($next = array_pop($gameStack)) {
#    echo count($gameStack)."\n";
    $i = $next->optimizePaddle();
        #echo "HEIA$i\n";
    $n = clone $next ;
    try {
        $n->play($i);
    } catch (End $e) {
        #echo "auda\n";
        continue;
    }
    #system("clear");
    #$n->printGrid();
    #usleep(100000);
    $gameStack[] = $n;
}
