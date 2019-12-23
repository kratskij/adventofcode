<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

require_once(__DIR__ . '/Robot.php');

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$code = $ir->trim(true)->explode(",");

$ips = $qs = [];
for ($i = 0; $i < 50; $i++) {
    $ips[] = new Robot($code, $i);
    $qs[] = [];
}

$nat = false;
$prevNatY = -1;

while (true) {
    $allStalled = true;
    foreach ($qs as $ip => $q) {
        if (!empty($qs[$ip])) {
            $n = array_shift($qs[$ip]);
        } else {
            $n = [-1,-1];
        }

        list($x, $y) = $n;
        try {
            $out = $ips[$ip]->read($x, $y);
        } catch (Stalled $e) {
            continue;
        }
        $allStalled = false;

        if ($out != null) {
            list($nip, $nx, $ny) = $out;
            if ($nip == 255) {
                if (!$nat) {
                    echo "Part 1: $ny\n";
                }
                $nat = [(int)$nx, (int)$ny];
            } else if ($nip >= 0 && $nip < 50) {
                $qs[(int)$nip][] = [(int)$nx, (int)$ny];
            }
        }
    }
    if ($allStalled && $nat) {
        $qs[0] = [$nat];
        if ($prevNatY === $nat[1]) {
            echo "Part 2: $prevNatY\n";
            die();
        }
        $prevNatY = $nat[1];
    }
}
