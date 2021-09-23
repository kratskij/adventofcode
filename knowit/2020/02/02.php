<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$input = ($test) ? 10000 : 5433000;

require_once(__DIR__."/../inputReader.php");
$throw = $gift = 0;
for ($i = 0; $i <= $input; $i++) {
    if (substr_count($i, "7") > 0) {
        for ($throw = $i; $throw > 0; $throw--) {
            if (primeCheck($throw)) {
                break;
            }
        }
        echo "at $i throwing $throw packages\n";
        $i += $throw;
    } else {
        echo "gifting $i\n";
        $gift++;
    }
}

var_dump($gift, $throw);

function primeCheck($number){
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
