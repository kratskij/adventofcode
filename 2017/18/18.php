<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
#$input = str_split($input[0]);
#$input = preg_match ("//" , $input, &$matches)
#var_dump($input);

$regex = "//";
$reg = [];
$outString = "";
$sum = [0 => 0, 1 => 0];
$c = 0;
$is = [
    0 => 0,
    1 => 0
];

$queue = [
    0 => [],
    1 => []
];
$regs = [
    0 => ["p" => 0, "i" => 0, "a" => 0, "b" => 0, "x" => [], "c" => 0, "d" => 0],
    1 => ["p" => 1, "i" => 0, "a" => 0, "b" => 0, "x" => [], "c" => 0, "d" => 0]
];

$prevCmd = [
    0 => "",
    1 => ""
];

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
    echo "VALUE".$value."\n";
    $to["queue"][] = $value;
    $from["sent"]++;
    $to["waiting"] = false;
}

$program =& $programs[0];
$otherProgram =& $programs[1];
while (true) {
    if (!isset($input[$program["i"]])) {
        echo $program["id"] . " is dead!\n";
        $program["dead"] = true;
    }
    if ($program["dead"] || $program["waiting"]) {
        $help = &$program;
        $program = &$otherProgram;
        $otherProgram = &$help;
        echo "head swap from {$otherProgram['id']} to {$program['id']}!\n";
        if ($program["dead"] || $program["waiting"]) {
            var_Dump($programs);
            die();
        }
    }
    $line = $input[$program["i"]];
    echo $program["id"] . "[line " . $program["i"] . "]: " . $line . "\n";
    $x = explode(" ", $line);
    $cmd = $x[0];
    switch($x[0]) {
        case "snd":
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
                echo "swapping!\n";
                echo $program["id"] .  ":" . $otherProgram["id"] . "=>";
                $program["waiting"] = true;
                #echo $program["id"] .  ":" . $otherProgram["id"] . "\n";
                continue 2;

            } else {
                $recv = array_shift($program["queue"]);
                if (!$recv) {
                    "HÆ";
                }
                echo $program["id"] . " rcv $recv, store in " . $x[1] . "\n";
                #echo count($queue[$k]) . " left in reg $k\n";
                #echo count($queue[$other]) . " left in reg $other\n";
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
                #e($k, "jgz " . h($reg, $x[2]) . ": ");
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
    #echo "swap! " . $a["id"] . "::" . $b["id"] . " -> ";
    $help = &$a;
    $a = &$b;
    $b = &$help;

    #echo $a["id"] . "::" . $b["id"] . "\n";
}
die();
/*
$k = 0;
while ($regs) {
    $i = &$is[$k];
    if (!isset($regs[$k])) {
        die('NEI');
    }
    $other = ($k == 0) ? 1 : 0;
    if (!isset($inputs[$k][$i])) {
        #unset($regs[$k]);
        echo "END OF PROGRAM $k";
        $k = $other;
        #var_dump($sum);
        continue;
    }
    $line = $inputs[$k][$i];
    $x = explode(" ", $line);
    $reg = &$regs[$k];
    $prevCmd[$k] = $x[0];
    #var_dump($prevCmd);
    switch($x[0]) {
        case "snd":
            e($k, "snd " . h($reg, $x[1], $k) . ":");
            #$lastSound = h($reg, $x[1]);
            echo "$k snd " . h($reg, $x[1], $k) . " to $other\n";
            #if (isset($queue[$other])) {
                $queue[$other][] = h($reg, $x[1], $k);
            #}
            $sum[$k]++;
            break;

        case "set":
            e($k, "set " . h($reg, $x[1], $k) . ": ");
            $reg[$x[1]] = h($reg, $x[2], $k);
            break;

        case "add":
            e($k, "add " . h($reg, $x[1], $k) . ": ");
            $reg[$x[1]] += h($reg, $x[2], $k);
            break;

        case "mul":
            e($k, "mul " . h($reg, $x[1], $k) . ": ");
            $reg[$x[1]] *= h($reg, $x[2], $k);
            break;

        case "mod":
            e($k, "mod " . h($reg, $x[1], $k) . ": ");

            $mod = h($reg, $x[2], $k);
            #if ($mod > 0) {
                #echo "$mod IS BIGGER THAN 0\n";
                #var_dump($sum);
                #try {
                    $reg[$x[1]] = $reg[$x[1]] % $mod;
                #} catch (DivisionByZeroError $e) {
                #    $reg[$x[1]] = 0;
                #    echo $e->getMessage($k,);
                #}
            #} else {
                #$reg[$x[1]] = 0;
            #}

            break;

        case "rcv":
            if (count($queue[$k]) == 0) {
                //wait
                $k = $other;
                echo "K set to $k\n";
                if ($prevCmd[$k] == "rcv" && count($queue[$k]) == 0) {
                    echo "DYING";
                    var_dump($sum);
                    die();
                } else {
                    echo $prevCmd[$k]."\n";
                }
                continue 2;

            } else {
                $recv = array_shift($queue[$k]);
                if (!$recv) {
                    "HÆ";
                }
                echo "$k rcv $recv, store in " . $x[1] . "\n";
                echo count($queue[$k]) . " left in reg $k\n";
                echo count($queue[$other]) . " left in reg $other\n";
                $reg[$x[1]] = $recv;
            }
            /*
                e($k, "rcv " . h($reg, $x[1]) . ": ");
                $rec = $lastSound;
                echo "LAASTSOUND " . $rec."\n";
                break 2;
            }
            *//*
            break;

        case "jgz":
            if (h($reg, $x[1]) > 0) {
                e($k, "jgz " . h($reg, $x[2]) . ": ");
                $i += h($reg, $x[2]);
                continue 2; // skip i increase
            }
            break;

        default:
            echo "NO!" . $x[0] . "|\n";
    }
    $i++;
}
*/
 var_dump($sum);

function getValue(&$program, $v) {
    if ($v == "p") {
        #if (isset($program["reg"]["p"]) && $program["id"] != substr($program["reg"]["p"], 0, 1)) {
        #    die("NOT THE SAME");
        #}
        return $program["id"];
    }
    if (is_numeric($v)) {
        return $v;
    }
    return $program["reg"][$v];
}
function e($k, $m) {
    echo "$k: $m\n";
}

function setReg(&$program, $reg, $value)
{
    echo $program["id"] . ":" . $reg . "[" . $program["reg"][$reg] . "] ";
    $program["reg"][$reg] = $value;
    echo " set to " . $program["reg"][$reg] . "\n";
}
#echo $sum;
#echo $outString;
