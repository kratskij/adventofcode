<?php

$dirs = [ [0,1], [1,0], [0,-1], [-1,0] ];
$allDirs = [ [0,1], [0,-1], [-1,-1], [-1,0], [-1,1], [1,-1], [1,0], [1,1] ];

function lcm(array $args) {
    foreach ($args as $arg) {
        if ($arg == 0) {
            return 0;
        }
    }
    if (empty($args)) {
        return 0;
    }
    if (count($args) == 1) {
        return reset($args);
    }
    if (count($args) == 2) {
        $m = array_shift($args);
        $n = array_shift($args);
        return abs(($m * $n) / gcd($m, $n));
    }

    return lcm(array_merge([array_shift($args)], [lcm($args)]));
}

function gcd($a, $b) {
   while ($b != 0) {
       $t = $b;
       $b = $a % $b;
       $a = $t;
   }
   return $a;
}


function isPrime($number){
    // 1 is not prime
    if ( $number == 1 ) {
        return false;
    }
    // 2 is the only even prime number
    if ( $number == 2 ) {
        return true;
    }
    // square root algorithm speeds up testing of bigger prime numbers
    $x = sqrt($number);
    $x = floor($x);
    for ( $i = 2 ; $i <= $x ; ++$i ) {
        if ( $number % $i == 0 ) {
            break;
        }
    }

    if( $x == $i-1 ) {
        return true;
    } else {
        return false;
    }
}
