<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");
$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();

$ans = 1;
$ans *= findMiddle($input, "en_US");
$ans *= findMiddle($input, "sv_SE");
$ans *= findMiddle($input, "nl_NL");

echo "Answer: $ans\n";

function findMiddle($lines, $locale) {
    #prepare lines
    foreach ($lines as $k => $line) {
        $lines[$k] = str_replace("'", "", $lines[$k]);
        $lines[$k] = str_replace(" ", "", $lines[$k]);

        if ($locale == "en_US" || $locale == "nl_NL") {
            $lines[$k] = Util::removeAccents($line);
        }
        if ($locale == "nl_NL") {
            preg_match("/^([a-z\s]+)(.*)\:(.*)$/", $lines[$k], $matches);
            if (isset($matches[1])) {
                $lines[$k] = $matches[2] . $matches[1] . ":" . $matches[3];
            }
        }
    }

    $coll = collator_create($locale);
    usort($lines, function($line1, $line2) use ($coll) {
        return collator_compare($coll, $line1, $line2);
    });

    return (int)trim(explode(":", $lines[(count($lines) / 2)])[1]);
}
