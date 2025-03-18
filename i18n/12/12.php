<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$ans = 1;
foreach (["en_US", "sv_SE", "nl_NL"] as $locale) {
    foreach ($lines = $input as $k => $line) {
        $lines[$k] = str_replace(" ", "", $lines[$k]);

        switch ($locale) {
            case "en_US":
                $lines[$k] = iconv("UTF-8", "ASCII//TRANSLIT", $lines[$k]);
                break;
            case "nl_NL":
                $lines[$k] = iconv("UTF-8", "ASCII//TRANSLIT", $lines[$k]);
                $lines[$k] = preg_replace("/^([a-z\s]+)(.*)\:(.*)$/", '$2$1:$3', $lines[$k]);
                var_dump($lines[$k]);
                break;
        }
    }

    collator_sort(collator_create($locale), $lines);

    $ans *= (int)trim(explode(":", $lines[(count($lines) / 2)])[1]);
}

echo "Answer: $ans\n";
