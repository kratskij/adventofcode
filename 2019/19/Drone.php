<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class Drone extends IntCodeComputer {

    public function isPulledAt($x, $y)  {
        $this->reset();
        $this->in($x, false);
        $this->in($y, true);

        return $this->out() == 1;
    }

    public function countBeamPixels($size) {
        $c = 0;
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $this->reset();
                if ($this->isPulledAt($x, $y)) {
                    $c++;
                }
            }
        }
        return $c;
    }

    public function findClosestOfSize($size) {
        $x = $y = 0;
        $stable = $prevX = false;
        $sizeSpan = $size - 1;

        while (true) {
            if ($this->isPulledAt($x, $y)) {
                // Found lower left
                if ($this->isPulledAt($x + $sizeSpan, $y - $sizeSpan)) {
                    // Found upper right; return upper left
                    return [$x, $y - $sizeSpan];
                }

                // Upper right not found. Try next row.
                $y++;

                if (!$stable) {
                    // Are we covered one step to the right? If we are, we are stable (guaranteed a hit on next row)
                    $stable = $this->isPulledAt($x + 1, $y);
                    // Later, we'll need to know if we've gone too far to the right
                    $prevX = $x;
                }

            } else {
                // Beam not yet found. Try one column to the right (unless unstable and too far right)
                if ($stable || $x < $prevX + 100) {
                    $x++;
                } else {
                    $x = $prevX;
                    $y++;
                }
            }
        }
    }
}
