<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

require_once(__DIR__ . '/Robot.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");

$r = (new Robot($code, true))
->read()
->south()
->take("hologram")
->north()
->west()
->take("mutex")
->east()
->north()
->north()
->north()
->take("semiconductor")
->south()
->west()
->north()
->take("jam")
->west()
->inv()
->north();
