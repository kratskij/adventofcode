<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$ir->trim(true);

$code = $ir->explode(",");
$code = $code;

require_once(__DIR__."/PaintRobot.php");

$paintRobot = new PaintRobot($code);
$paintRobot->paint(PaintRobot::BLACK);
$paintCount = $paintRobot->getPanelPaintCount();
$paintRobot->reset();
$paintRobot->paint(PaintRobot::WHITE, true);
system("clear");
$regId = $paintRobot->getRegistrationIdentifier();
echo $regId;
#echo "Part 1: $paintCount\n";
#echo "Part 2: $regId\n";
