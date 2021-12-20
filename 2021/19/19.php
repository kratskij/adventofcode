<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();
$p1 = $p2 = 0;

$expectMatches = ($file == "test2" ? 3 : 12);

$scanners = [];
foreach ($input as $k => $line) {
    if (explode(" ", $line)[0] === "---") {
        $scannerId = (int)explode(" ", $line)[2];
        continue;
    }
    if ($line == "") {
        continue;
    }
    $coords = array_map("intval", explode(",", $line));
    $idx = implode(",", $coords);
    $scanners[$scannerId][$idx] = [
        "coords" => $coords,
        "variations" => rotate3d($coords),
        "distances" => [],
    ];
}


foreach ($scanners as $scannerId => $beacons) {
    setDistances($beacons);
    $scanners[$scannerId] = $beacons;
}

$truth = [];
foreach ($scanners[0] as $beacon) {
    list($x, $y, $z) = $beacon["coords"];
    $idx = implode(",", $beacon["coords"]);
    $truth[$idx] = [
        "coords" => $beacon["coords"],
        "distances" => [],
    ];
}

setDistances($truth);
$scannerPos = [];

$change = true;
while ($change) {
    $change = false;
    foreach ($scanners as $scannerId => $beacons) {
        if ($scannerId == 0) {
            continue;
        }

        foreach ($beacons as $beaconId => $beacon) {
            foreach ($truth as $truthBeacon) {
                if (count(array_intersect($truthBeacon["distances"], $beacon["distances"])) < $expectMatches - 1) {
                    continue;
                }
                list($tx,$ty,$tz) = $truthBeacon["coords"];

                foreach ($beacon["variations"] as $variationId => $variation) {
                    list($x,$y,$z) = $variation;
                    $scannerX = $tx-$x;
                    $scannerY = $ty-$y;
                    $scannerZ = $tz-$z;
                    $found = 1;
                    foreach ($beacon["distances"] as $matchBeaconId => $null) {
                        list($ox,$oy,$oz) = $beacons[$matchBeaconId]["variations"][$variationId];
                        $idx = ($scannerX + $ox) . "," . ($scannerY + $oy) . "," . ($scannerZ + $oz);
                        if (isset($truth[$idx])) {
                            $found++;
                        }
                    }
                    if ($found >= $expectMatches) {
                        foreach ($beacons as $beaconId => $newBeacon) {
                            list($nx,$ny,$nz) = $newBeacon["variations"][$variationId];
                            $newBeacon["coords"] = [$scannerX + $nx, $scannerY + $ny, $scannerZ + $nz];
                            $newBeacon["distances"] = [];
                            unset($newBeacon["variations"]);
                            $idx = implode(",", $newBeacon["coords"]);
                            if (!isset($truth[$idx])) {
                                $change = true;
                            }
                            $truth[$idx] = $newBeacon;
                        }
                        setDistances($truth);
                        $scannerPos[$scannerId] = [$scannerX,$scannerY,$scannerZ];
                        unset($scanners[$scannerId]);
                        continue 4;
                    }
                }
            }
        }
    }
}

$p1 = count($truth);
$p2 = 0;
foreach ($scannerPos as $coords) {
    list($x,$y,$z) = $coords;
    foreach ($scannerPos as $coords) {
        list($x2,$y2,$z2) = $coords;
        $p2 = max($p2, abs($x2-$x) + abs($y2-$y) + abs($z2-$z));
    }
}

echo "P1: $p1\nP2: $p2\n";

function setDistances(&$beacons) {
    foreach ($beacons as $beacon) {
        list($x, $y, $z) = $beacon["coords"];
        $beaconId = implode(",", $beacon["coords"]);
        foreach ($beacons as $beacon2) {
            $beaconId2 = implode(",", $beacon2["coords"]);
            if ($beaconId2 == $beaconId) {
                continue;
            }
            list($x2, $y2, $z2) = $beacon2["coords"];
            $distance = abs($x-$x2) + abs($y-$y2) + abs($z-$z2);

            $beacons[$beaconId]["distances"][$beaconId2] = $distance;
        }
    }
}

function rotate3d($coords) {
    list($x,$y,$z) = $coords;

    $variations = [];
    flipit($variations, $x, $y, $z, "x", "y", "z");
    flipit($variations, -$x, -$y, $z, "-x", "-y", "z"); // turn 180 deg
    flipit($variations, -$y, $x, $z, "-y", "x", "z"); // turn 90deg right
    flipit($variations, $y, -$x, $z, "y", "-x", "z"); // turn 90deg left

    return $variations;
}

function flipit(&$variations, $x, $y, $z, $nx, $ny, $nz) {
    $variations[t("$nx,$ny,$nz")] = [$x,$y,$z]; // normal position
    $variations[t("$nx,$nz,-$ny")] = [$x,$z,-$y]; // quarter flip forward
    $variations[t("$nx,-$nz,$ny")] = [$x,-$z,$y]; // quarter flip backward
    $variations[t("$nz,$ny,-$nx")] = [$z,$y,-$x]; // quarter flip to the right
    $variations[t("-$nz,$ny,$nx")] = [-$z,$y,$x]; // quarter flip to the left
    $variations[t("$nx,-$ny,-$nz")] = [$x,-$y,-$z]; // half flip forward/backward
    $variations[t("-$nx,$ny,-$nz")] = [-$x,$y,-$z]; // half flip left/right
}

function t($str) {
    return str_replace("--", "", $str);
}
