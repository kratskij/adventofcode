<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$program = [
    "id" => 0,
    "reg" => ["a" => 0, "b" => 0, "c" => 0, "d" => 0, "e" => 0, "f" => 0, "g" => 0, "h" => 0],
    "i" => 0,
];

while (true) {
    if (!isset($input[$program["i"]])) {
        break;
    }
    $c++;
    $line = $input[$program["i"]];
    $x = explode(" ", $line);
    switch($x[0]) {
        case "set":
            setReg($program, $x[1], getValue($program, $x[2]));
            break;
        case "sub":
            setReg($program, $x[1], $program["reg"][$x[1]] - getValue($program, $x[2]));
            break;
        case "mul":
            setReg($program, $x[1], $program["reg"][$x[1]] * getValue($program, $x[2]));
            $mul++;
            break;
        case "jnz":
            if (getValue($program, $x[1]) != 0) {
                $program["i"] += getValue($program, $x[2]);
                continue 2; // skip i increase
            }
            break;
        default:
            echo "NO!" . $x[0] . "|\n";
    }

    $program["i"]++;
}

echo "Part 1: $mul\n";

$c = 0;
for ($num = 107900; $num <= 124900; $num += 17) {
    for ($i = 2; $i < $num; $i++) {
        if (($num % $i) == 0) {
            $c++;
            break;
        }
    }
}
echo "Part 2: $c\n";

function getValue(&$program, $v) {
    if (is_numeric($v)) {
        return $v;
    }
    return (isset($program["reg"][$v])) ? $program["reg"][$v] : 0;
}

function setReg(&$program, $reg, $value)
{
    $program["reg"][$reg] = $value;
}
