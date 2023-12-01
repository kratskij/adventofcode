<?php

class Util {
    public static function printGrid($grid, $minY = PHP_INT_MAX, $maxY = -PHP_INT_MAX, $minX = PHP_INT_MAX, $maxX = -PHP_INT_MAX) {
        #$minY = $minX = $min;
        #$maxY = $maxX = $max;
        foreach ($grid as $y => $row) {
            $minY = min($minY, $y);
            $maxY = max($maxY, $y);
            $minX = min($minX, min(array_keys($row)));
            $maxX = max($maxX, max(array_keys($row)));
        }

        $out = "";
        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                if (!isset($grid[$y][$x])) {
#                    echo "not set at $y,$x\n";
                    $out .= "░";
                } else {
                    if (is_array($grid[$y][$x])) {
#                        var_dump($grid[$y][$x]);
                        if (count($grid[$y][$x]) > 1) {
                            $out .= count($grid[$y][$x]);
                        } else {
                            $out .= $grid[$y][$x][0];
                        }

                    } else {
                        $out .= $grid[$y][$x];
                    }
                }
            }
            $out .= "\n";
        }

        return $out."\n\n";
    }

    public static function printTetris($grid, $rock, $rockY, $rockX) {
        foreach ($rock as $cy => $cxs) {
            foreach ($cxs as $cx) {
                $grid[$rockY+$cy][$rockX+$cx] = false;
            }
        }
        $minY = $minX = PHP_INT_MAX;
        $maxY = $maxX = -PHP_INT_MAX;
        foreach ($grid as $y => $row) {
            $minY = min($minY, $y);
            $maxY = max($maxY, $y);
            $minX = min($minX, min(array_keys($row)));
            $maxX = max($maxX, max(array_keys($row)));
        }

        $out = "";
        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                if (!isset($grid[$y][$x])) {
                    $out .= " ";
                } else if ($grid[$y][$x] === true) {
                    $out .= "█";
                } else if ($grid[$y][$x] === false) {
                    $out .= "#";
                }
            }
            $out .= "\n";
        }

        echo $out."\r\n\r\n";
    }
}
