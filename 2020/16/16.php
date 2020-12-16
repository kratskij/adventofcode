<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
list($fieldRanges, $myTicket, $allTickets) = $ir->explode("\n\n");

$fieldRanges = explode("\n", $fieldRanges);
$myTicket = array_map("intval", explode(",", explode("\n", $myTicket)[1]));
$allTickets = explode("\n", $allTickets);
unset($allTickets[0]);
foreach ($allTickets as $i => $ticket) {
    $allTickets[$i] = array_map("intval", explode(",", $ticket));
}


$validator = [];
foreach ($fieldRanges as $k => $line) {
    list($name, $ranges) = explode(": ", $line);
    $ranges = explode(" or ", $ranges);
    foreach ($ranges as $p) {
        list($min, $max) = array_map("intval", explode("-", $p));
        for ($i = $min; $i <= $max; $i++) {
            $validator[$name][$i] = $i;
        }
    }
}

$p1 = 0;
foreach ($allTickets as $ticketNumber => $ticket) {
    foreach ($ticket as $order => $value) {
        $validInt = false;
        foreach ($validator as $boundaries) {
            if (isset($boundaries[$value])) {
                $validInt = true;
            }
        }
        if (!$validInt) {
            $p1 += $value;
            unset($allTickets[$ticketNumber]);
        }
    }
}

$candidates = [];
foreach ($validator as $name => $boundaries) {
    for ($fieldNumber = 0; $fieldNumber < count($validator); $fieldNumber++) {
        $allIsMatch = true;
        foreach ($allTickets as $ticket) {
            if (!isset($boundaries[$ticket[$fieldNumber]])) {
                $allIsMatch = false;
            }
        }
        if ($allIsMatch) {
            $candidates[$fieldNumber][$name] = true;
        }
    }
}

$change = true;
while ($change) {
    $change = false;
    foreach ($candidates as $field => $cand) {
        if (is_array($cand) && count($cand) == 1) {
            $candidates[$field] = key($cand);
        }
        if (!is_array($candidates[$field])) {
            $onlyField = $candidates[$field];
            foreach ($candidates as $field2 => $cand2) {
                if (is_array($cand2) && isset($cand2[$onlyField])) {
                    unset($candidates[$field2][$onlyField]);
                    $change = true;
                }
            }
        }
    }
}

$p2 = 1;
foreach ($myTicket as $field => $value) {
    if (substr($candidates[$field], 0, 9) == "departure") {
        $p2 *= $value;
    }
}

echo "P1: $p1\nP2: $p2\n";
