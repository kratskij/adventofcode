<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$pairs = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true)->csv(", ");
$wIdx = array_flip((new InputReader(__DIR__ . DIRECTORY_SEPARATOR . "wordlist"))->trim(true)->lines());

$wIdx = array_flip(file(__DIR__ . DIRECTORY_SEPARATOR . "wordlist", FILE_IGNORE_NEW_LINES));
$pairs = array_map(
    function($line) {
        return explode(", ", $line);
    },
    file(__DIR__ . DIRECTORY_SEPARATOR . "input", FILE_IGNORE_NEW_LINES)
);

echo array_sum(array_map("mb_strlen", array_unique(array_filter(array_keys($wIdx), function($word) use ($pairs, $wIdx) {
    return count(array_filter($pairs, function($pair) use ($wIdx, $word) {
        return isset($wIdx[$pair[0].$word]) && isset($wIdx[$word.$pair[1]]);
    })) > 0;
}))));
die();
/*foreach ($wIdx as $word => $_) {
    foreach ($pairs as $pair) {
        if (isset($wIdx[$pair[0] . $word]) && isset($wIdx[$word . $pair[1]])) {
            $found[$word] = true;
        }
    }
}*/
echo array_sum(array_map("mb_strlen", array_keys($found)));
#302: WRONG
#308: WRONG
#278: CORRECT
