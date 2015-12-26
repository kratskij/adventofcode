<?php

$test = false;

$file = ($test) ? "test.txt" : "input";
$input = explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file));

$packages = array_map(function($i) {
	return (int)$i;
}, $input);

$best = bestQE($packages, 3);

echo "Part 1: " . bestQE($packages, 3) . "\n";
echo "Part 2: " . bestQE($packages, 4) . "\n";

function bestQE($packages, $compartments, $mustMatch = false, &$best = false, $used = []) {
    if (!$mustMatch) {
    	$mustMatch = array_sum($packages)/$compartments;
    }
    $packages = array_filter(
    	$packages,
    	function($v) use ($mustMatch) {
    		return ($v <= $mustMatch);
    	}
    );

    while ($size = array_shift($packages)) {
    	$cUsed = $used;
        $cUsed[] = $size;

        if ($best && count($cUsed) > count($best["packages"])) { return; }

        if ($size == $mustMatch) {
    		$QE = array_reduce($cUsed, function($carry, $item) {
		    	$carry *= $item;
		    	return $carry;
		    }, 1);
	    	
	    	if ($best && $QE > $best["QE"]) { continue; }
	    	
	    	$best = [
            	"QE" => $QE,
            	"packages" => $cUsed
            ];
            #var_dump($best);
        } else {
            bestQE($packages, $compartments, $mustMatch - $size, $best, $cUsed);
        }
    }
    return $best["QE"];
}
