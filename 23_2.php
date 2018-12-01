<?php
$a = $b = $c = $d = $e = $f = $g = $h = 0;

$b = 107900;
$c = $b + 17000;
$a = 1;

while(true) {
    func3($a, $b, $c, $d, $e, $f, $g, $h);
    echo "4: $a\t$b\t$c\t$d\t$e\t$f\t$g\t$h\n";
}

function func3(&$a, &$b, &$c, &$d, &$e, &$f, &$g, &$h) {
    $f = 1;
    $d = 2;

    func2($a, $b, $c, $d, $e, $f, $g, $h);
    while ($g != 0) {
        func2($a, $b, $c, $d, $e, $f, $g, $h);
    }

    if ($f == 0) {
        $h += 1;
    }
    $g = $b;
    $g -= $c;
    if ($g == 0) {
        echo "Part 2: $h\n";
        exit;
    }
    $b += 17;
    echo "3: $a\t$b\t$c\t$d\t$e\t$f\t$g\t$h\n";
}


function func2(&$a, &$b, &$c, &$d, &$e, &$f, &$g, &$h) {
    #$d -= 1;
    #$g = $d - $b;
    #echo "2.2: $a\t$b\t$c\t$d\t$e\t$f\t$g\t$h\n";
    #return;
    $e = 2;
    func1($a, $b, $c, $d, $e, $f, $g, $h);

    while ($g != 0) {
        func1($a, $b, $c, $d, $e, $f, $g, $h);
    }

    $d += 1;
    $g = $d;
    $g -= $b;
    echo "2: $a\t$b\t$c\t$d\t$e\t$f\t$g\t$h\n";
}

function func1(&$a, &$b, &$c, &$d, &$e, &$f, &$g, &$h)
{
    $g = $d;
    $g *= $e;
    $g -= $b;
    if ($g == 0) {
        $f = 0;
    }
    $e += 1;
    $g = $e;
    $g -= $b;
}
