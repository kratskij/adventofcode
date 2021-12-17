<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->chars();

$p1 = $p2 = false;
$bin = "";

foreach ($input as $k => $hex) {
    $bin .= str_pad(decbin(hexdec($hex)), 4, "0", STR_PAD_LEFT);
}

list($result, $versionSum, $readBytes) = parseBin($bin);
$p1 = $versionSum;
$p2 = $result;

echo "P1: $p1\nP2: $p2\n";

function parseBin($bin) {
    $versionSum = 0;
    $readBytes = 0;

    $version = bindec(substr($bin, 0, 3));
    $type = bindec(substr($bin, 3, 3));
    $rem = substr($bin, 6);
    $readBytes += 6;

    $versionSum += $version;

    if ($type == 4) {
        while ((strlen($rem) % 4) != 0) {
            $rem .= "0";
        }
        $rb = 0;
        $v = literalValue($rem, $rb);
        $readBytes += $rb;
        $return = $v;
    } else {
        $lengthTypeId = substr($rem, 0, 1);
        $rem = substr($rem, 1);
        $readBytes += 1;

        $offset = ($lengthTypeId == "0") ? 15 : 11;
        $crit = bindec(substr($rem, 0, $offset));

        $rem = substr($rem, $offset);
        $readBytes += $offset;

        $vals = [];
        $rb = $i = 0;
        while (
            ($lengthTypeId == "0" && $rb < $crit) ||
            ($lengthTypeId == "1" && $i++ < $crit)
        ) {
            list($v, $s, $b) = parseBin(substr($rem, $rb));
            $rb += $b;
            $versionSum += $s;
            $vals[] = $v;
        }
        $readBytes += $rb;

        switch ($type) {
            case 0:
                $return = array_sum($vals);
                break;
            case 1:
                $return = array_product($vals);
                break;
            case 2:
                $return = min($vals);
                break;
            case 3:
                $return = max($vals);
                break;
            case 5:
                $return = ($vals[0] > $vals[1]) ? 1 : 0;
                break;
            case 6:
                $return = ($vals[0] < $vals[1]) ? 1 : 0;
                break;
            case 7:
                $return = ($vals[0] == $vals[1]) ? 1 : 0;
                break;
            default:
                throw new Exception("dust");
        }
    }

    return [$return, $versionSum, $readBytes];
}


function literalValue($ss, &$readBytes) {
    $subPackets = [];
    for ($i = 0; $i < strlen($ss); $i+=5) {
        $prefix = $ss[$i];
        $subPackets[] = substr($ss, $i+1, 4);
        if ($prefix == 0) {
            break;
        }
    }
    $val = bindec(implode("", $subPackets));

    $readBytes += $i+5;
    return $val;
}
