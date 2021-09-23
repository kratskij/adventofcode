<?php

$count = 0;
$cache = [];
for ($i = 1; $i <= 100000; $i++) {
    $m = [1 => 1, $i => $i];
    $sum = divisorSum2($i, $m, $c);
    $diff =  $sum - 2 * $i;

    echo "found $i: $sum\n";
    if ($diff > 0 && filter_var(sqrt($diff), FILTER_VALIDATE_INT) !== false) {
        $count++;
    }
}
echo $count."\n";

function divisorSum($num) {
    $sum = 0;
    for ($i = (int)sqrt($num); $i >= 1; $i--) {
        if ($num % $i == 0) {
            $sum += $i;
            if ($i * $i != $num) {
                $sum += $num / $i;
            }
        }
    }
    return $sum;
}

function divisorSum2($num, &$all, &$c) {
    static $primes;
    if ($primes === null) {
        $primes = [];
        $c = [];
        for ($i = 2; $i < 1000000; $i++) {
            if (isPrime($i)) {
                $primes[$i] = $i;
                $c[$i] = [$i => $i];
            }
        }

    }
    if (isset($c[$num])) {
        echo "found $num in cache: " . array_sum($c[$num]) . " !\n";
        return array_sum($c[$num]);
    }

    $localSum = 0;
    foreach ($primes as &$prime) {
        if ($prime > $num) {
            break;
        }
        if (($num % $prime) == 0) {
            $next = $num / $prime;
            if (!isset($all[$next]) && $next === (int)$next) {
                if (isset($c[$next])) {
                    $all = $all + $c[$next];
                } else {
                    $lSum = divisorSum2($next, $all, $c);
                    $c[$next][$lSum] = $lSum;
                }
                $all[$next] = $next;
            }
        }
    }

    return array_sum(array_keys($all));
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
