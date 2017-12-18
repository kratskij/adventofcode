<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

#$sum = [0 => 0, 1 => 0];

$programs = [
    0 => [
        "id" => 0,
        "reg" => ["p" => 0],
        "i" => 0,
        "waiting" => false,
        "dead" => false,
        "queue" => [],
        "sent" => 0
    ],
    1 => [
        "id" => 1,
        "reg" => ["p" => 1],
        "i" => 0,
        "waiting" => false,
        "dead" => false,
        "queue" => [],
        "sent" => 0
    ],
];

function send($value, &$from, &$to) {
    $to["queue"][] = $value;
    $from["sent"]++;
    $to["waiting"] = false;
}

$program =& $programs[0];
$otherProgram =& $programs[1];
while (true) {
    if (!isset($input[$program["i"]])) {
        $program["dead"] = true;
    }
    if ($program["dead"] || $program["waiting"]) {
        $help = &$program;
        $program = &$otherProgram;
        $otherProgram = &$help;
        if ($program["dead"] || $program["waiting"]) {
            echo "Part 2: " . $programs[1]["sent"] . "\n";
            die();
        }
    }
    $line = $input[$program["i"]];
    $x = explode(" ", $line);
    $cmd = $x[0];
    switch($x[0]) {
        case "snd":
            #$lastSound = h($reg, $x[1]);
            send(getValue($program, $x[1]), $program, $otherProgram);
            break;
        case "set":
            setReg($program, $x[1], getValue($program, $x[2]));
            break;
        case "add":
            setReg($program, $x[1], $program["reg"][$x[1]] + getValue($program, $x[2]));
            break;
        case "mul":
            setReg($program, $x[1], $program["reg"][$x[1]] * getValue($program, $x[2]));
            break;
        case "mod":
            setReg($program, $x[1], getValue($program, $x[1]) % getValue($program, $x[2]));
            break;
        case "rcv":
            if (count($program["queue"]) == 0) {
                //wait
                $program["waiting"] = true;
                continue 2;

            } else {
                $recv = array_shift($program["queue"]);
                if (!$recv) {
                    "HÆ";
                }
                setReg($program, $x[1], $recv);
            }
            /*
                e($k, "rcv " . getValue($reg, $x[1]) . ": ");
                $rec = $lastSound;
                echo "LAASTSOUND " . $rec."\n";
                break 2;
            }
            */
            break;

        case "jgz":
            if (getValue($program, $x[1]) > 0) {
                $program["i"] += getValue($program, $x[2]);
                continue 2; // skip i increase
            }
            break;

        default:
            echo "NO!" . $x[0] . "|\n";
    }
    $program["i"]++;
}
var_Dump($sum);
function swap(&$a, &$b)
{
    $help = &$a;
    $a = &$b;
    $b = &$help;

}

function getValue(&$program, $v) {
    if (is_numeric($v)) {
        return $v;
    }
    return $program["reg"][$v];
}

function setReg(&$program, $reg, $value)
{
    $program["reg"][$reg] = $value;
}
