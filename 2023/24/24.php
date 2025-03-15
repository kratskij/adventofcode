<?php

ini_set('memory_limit','2048M');
bcscale(3);
$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
#require_once __DIR__."/../Util.php";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->extractNumbers(true);

if ($test) {
    $min = 7;
    $max = 27;
} else {
    $min = 200000000000000;
    $max = 400000000000000;
}

// Turning point (found manually by finding when lowest distance switches from decreasing to increasing)
#$i = 313932782800;
#var_dump(findCollisionPoint3d([[1,0,0],[3,0,0]], [[0,1,-1],[0,1,5]]));die();
$i = 3;
$prevTotalDist = PHP_INT_MAX;
while ( false && true) {
    $totalDistance = 0;
    foreach ($input as $k => $line) {
        [$px, $py, $pz, $vx, $vy, $vz] = $line;

        $edgePoints = [[$px, $py, $pz], [$px+$vx, $py+$vy, $pz+$vz]];        
        foreach ($input as $k2 => $line2) {
            if ($k == $k2) {
                continue;
            }
            
            [$px2, $py2, $pz2, $vx2, $vy2, $vz2] = $line2;
            $edgePoints2 = [[$px2, $py2, $pz2], [$px2+$vx2, $py2+$vy2, $pz2+$vz2]];
            
            /*$pt = findCollisionPoint3d($edgePoints, $edgePoints2);
            if ($pt) {
                $xd = $pt[0]-$px2;
                $yd = $pt[1]-$py2;
                $zd = $pt[2]-$pz2;

                foreach ($input as $k3 => $line3) {
                    if ($k == $k3) {
                        continue;
                    }
                    
                    [$px3, $py3, $pz3, $vx3, $vy3, $vz3] = $line3;
                    $edgePoints3 = [[$px3, $py3, $pz3], [$px3+$vx3, $py3+$vy3, $pz3+$vz3]];

                    $pt2 = findCollisionPoint3d($edgePoints2, $edgePoints3);
                    if ($pt2) {
                        $xd2 = $pt2[0]-$px2;
                        $yd2 = $pt2[1]-$py2;
                        $zd2 = $pt2[2]-$pz2;
                        if ($xd2 && $xd2 == $xd && $yd2 && $yd2 == $yd && $zd2 && $zd2 == $zd) {
                            var_dump($line, $line2, $line3, $pt, $pt2);
                            die ("OH MY FUCKING GOD");
                        }
                    }
                }
            }
            var_dump($pt);*/

            $xd = ($px2+$vx2*($i+1)) - ($px+$vx*$i);
            $yd = ($py2+$vy2*($i+1)) - ($py+$vy*$i);
            $zd = ($pz2+$vz2*($i+1)) - ($pz+$vz*$i);
            
            foreach ($input as $k3 => $line3) {
                if ($k3 == $k || $k3 == $k2) {
                    continue;
                }
                [$px3, $py3, $pz3, $vx3, $vy3, $vz3] = $line3;
                $xd2 = ($px3+$vx3*($i+2)) - ($px2+$vx2*($i+1));
                $yd2 = ($py3+$vy3*($i+2)) - ($py2+$vy2*($i+1));
                $zd2 = ($pz3+$vz3*($i+2)) - ($pz2+$vz2*($i+1));
                #echo "at $i ($px,$py,$pz), comparing $xd==$xd2, $yd==$yd2, $zd==$zd2\n";
                if ($xd2 == $xd && $yd2 == $yd && $zd2 == $zd) {
                    echo "FOUND A LINE AFTER $i AT $px,$py,$pz ($px2,$py2,$px2) ($px3,$py3,$px3)\n";
                    
                    $matches = [];
                    $minTime = PHP_INT_MAX;
                    for ($d = 0; $d < count($input); $d++) {
                        foreach ($input as $k4 => $line4) {
                            if ($k4 == $k && $d > 0) {
                                continue;
                            }
                            [$px4, $py4, $pz4, $vx4, $vy4, $vz4] = $line4;

                            foreach ([-$d, $d] as $offset) {
                                $expectedX = ($px+$vx*$i) + $xd * $offset;
                                $expectedY = ($py+$vy*$i) + $yd * $offset;
                                $expectedZ = ($pz+$vz*$i) + $zd * $offset;
                                $x4 = $px4 + $vx4 * ($i+$offset);
                                $y4 = $py4 + $vy4 * ($i+$offset);
                                $z4 = $pz4 + $vz4 * ($i+$offset);
                                echo "at $i+$offset ($px4,$py4,$pz4): expected $expectedX,$expectedY,$expectedZ, got $x4,$y4,$z4\n";
                                if ($x4 == $expectedX && $y4 == $expectedY && $z4 == $expectedZ) {
                                    $matches[$k4] = [$i+$offset, $expectedX, $expectedY, $expectedZ];
                                    echo "found a match ($px4,$py4,$pz4) at $i+$offset\n";
                                    if ($i+$offset < $minTime) {
                                        $minTime = min($minTime, $i+$offset);
                                        $minCoords = [
                                            $expectedX - $xd,
                                            $expectedY - $yd,
                                            $expectedZ - $zd,
                                        ];
                                    }
                                    if (count($matches) == count($input)) {
                                        $p2 = array_sum($minCoords);
                                        echo "yay $p2\n";
                                        break 7;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    echo "$i: $totalDistance\n";
    $i++;
}

foreach ($input as $k => $line) {
    [$px, $py, $pz, $vx, $vy, $vz] = $line;
    #$pts = findEdgePoints3d($min, $max, $px,$vx,$py,$vy,$pz,$vz);
    #echo (json_encode($pts)) . "\n";
}

$collisions = collisions($input, $min, $max);

$ks = [];
$p1 = count(array_filter($collisions, function($data) use ($min, $max, &$ks) {
    [$point, $k, $k2] = $data;
    $ks[$k][$k2] = $point;
    $ks[$k2][$k] = $point;
    return (max($point) <= $max && min($point) >= $min);
}));
$maxks = 0;
$thek = [];
foreach ($ks as $k => $vals) {
    if (count($vals) > 0) {
        #echo "omg at $k (" . count($vals) . ")\n";
        if (count($vals) > $maxks) {
            $maxks = max($maxks, count($vals));
            $thek = [$k];
        } else if (count($vals) == $maxks) {
            $thek[] = $k;
        }
    }
}
$p2 = $maxks . " " .implode(",", $thek);

function collisions($input, $min, $max) {
    $collisions = [];
    foreach ($input as $k => $line) {
        [$px, $py, $pz, $vx, $vy, $vz] = $line;

        $edgePoints = [[$py, $pz], [bcadd($py,$vy), bcadd($pz,$vz)]];

        foreach ($input as $k2 => $line2) {
            if ($k <= $k2) {
                continue;
            }
            [$px2, $py2, $pz2, $vx2, $vy2, $vz2] = $line2;
            $edgePoints2 = [[$py2, $pz2], [bcadd($py2,$vy2), bcadd($pz2,$vz2)]];

            $collisionPoint = findCollisionPoint($edgePoints, $edgePoints2);
            [$cy,$cz] = $collisionPoint;
            if (
                /*$cx < $min || $cy < $min || $cx > $max || $cy > $max ||*/
                ($cy > $py && $vy < 0) || ($cz > $pz && $vz < 0) ||
                ($cy < $py && $vy > 0) || ($cz < $pz && $vz > 0) ||
                ($cy > $py2 && $vy2 < 0) || ($cz > $pz2 && $vz2 < 0) ||
                ($cy < $py2 && $vy2 > 0) || ($cz < $pz2 && $vz2 > 0)
            ) {
                // crossed in the past
                continue;
            }
            $collisions[] = [$collisionPoint, $k, $k2];
        }
    }
    return $collisions;
}

echo "P1: $p1\nP2: $p2\n";

function findCollisionPoint($line1, $line2) {
    [$p1, $p2] = $line1;
    [$p3, $p4] = $line2;

    [$x1, $y1] = $p1;
    [$x2, $y2] = $p2;
    [$x3, $y3] = $p3;
    [$x4, $y4] = $p4;

    $uA = @(
        bcdiv(
            bcsub(
                bcmul(bcsub($x4, $x3), bcsub($y1 ,$y3)),
                bcmul(bcsub($y4, $y3), bcsub($x1 ,$x3))
            ),
            bcsub(
                bcmul(bcsub($y4, $y3), bcsub($x2, $x1)),
                bcmul(bcsub($x4, $x3), bcsub($y2, $y1))
            )
        )
    );
    #$uB = @((($x2-$x1)*($y1-$y3) - ($y2-$y1)*($x1-$x3)) / (($y4-$y3)*($x2-$x1) - ($x4-$x3)*($y2-$y1)));

    return [
        bcadd($x1, bcmul($uA, bcsub($x2, $x1))),
        bcadd($y1, bcmul($uA, bcsub($y2, $y1))),
    ];
}

function findCollisionPoint3d($line1, $line2) {
    [$p1, $p2] = $line1;
    [$p3, $p4] = $line2;
   // Algorithm is ported from the C algorithm of 
   // Paul Bourke at http://local.wasp.uwa.edu.au/~pbourke/geometry/lineline3d/
 
    [$x1, $y1, $z1] = $p1;
    [$x2, $y2, $z2] = $p2;
    [$x3, $y3, $z3] = $p3;
    [$x4, $y4, $z4] = $p4;
    $p13 = [$x1 - $x3, $y1 - $y3, $z1 - $z3];
    $p43 = [$x4 - $x3, $y4 - $y3, $z4 - $z3];
    $p21 = [$x2 - $x1, $y2 - $y1, $z2 - $z1];
 
    $d1343 = $p13[0] * $p43[0] + $p13[1] * $p43[1] + $p13[2] * $p43[2];
    $d4321 = $p43[0] * $p21[0] + $p43[1] * $p21[1] + $p43[2] * $p21[2];
    $d1321 = $p13[0] * $p21[0] + $p13[1] * $p21[1] + $p13[2] * $p21[2];
    $d4343 = $p43[0] * $p43[0] + $p43[1] * $p43[1] + $p43[2] * $p43[2];
    $d2121 = $p21[0] * $p21[0] + $p21[1] * $p21[1] + $p21[2] * $p21[2];
 
    $denom = $d2121 * $d4343 - $d4321 * $d4321;
    if (abs($denom) <= 0) {
       return false;
    }
    $numer = $d1343 * $d4321 - $d1321 * $d4343;
 
    $mua = $numer / $denom;
    $mub = ($d1343 + $d4321 * ($mua)) / $d4343;
 
    return [
        $p1[0] + $mua * $p21[0],
        $p1[1] + $mua * $p21[1],
        $p1[2] + $mua * $p21[2],
        $p3[0] + $mub * $p43[0],
        $p3[1] + $mub * $p43[1],
        $p3[2] + $mub * $p43[2],
    ];
}
