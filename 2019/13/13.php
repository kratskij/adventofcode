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
echo "Part 1: " . $arcadeCabinet->countBlocks() . "\n";
$arcadeCabinet->autoPlay(true);
echo "Part 2: " . $arcadeCabinet->getHighScore() . "\n";
