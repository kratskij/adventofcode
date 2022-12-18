<?php

class Util {
    public static function printGrid($grid) {
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
                    echo "not set at $y,$x\n";
                }
                if (!$grid[$y][$x]) {
                    $out .= "░";
                } else {
                    $out .= "█";
                }
            }
            $out .= "\n";
        }

        return $out;
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
