<?php
if (!function_exists('mb_str_split')) {
    function mb_str_split($string, $split_length = 1)
    {
        if ($split_length == 1) {
            return preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY);
        } elseif ($split_length > 1) {
            $return_value = [];
            $string_length = mb_strlen($string, "UTF-8");
            for ($i = 0; $i < $string_length; $i += $split_length) {
                $return_value[] = mb_substr($string, $i, $split_length, "UTF-8");
            }
            return $return_value;
        } else {
            return false;
        }
    }
}

$test = (isset($argv[1]) && $argv[1] == "test");

$words = ($test)
    ? ["kakao", "kriminalroman", "kvikklunch", "kylling", "langfredag", "langrennski", "palmesøndag", "påskeegg", "smågodt", "solvegg", "yatzy"]
    : ["nisseverksted", "pepperkake", "adventskalender", "klementin", "krampus", "juletre", "julestjerne", "gløggkos", "marsipangris", "mandel", "sledespor", "nordpolen", "nellik", "pinnekjøtt", "svineribbe", "lutefisk", "medisterkake", "grevinne", "hovmester", "sølvgutt", "jesusbarnet", "julestrømpe", "askepott", "rudolf", "akevitt"];
    #: ["hovmester"];

$words = array_fill_keys($words, true);
foreach ($words as $w => $none) {
    $letters = mb_str_split($w);
    $chars[$letters[0]][$w] = $letters;
}

$input = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . ($test ? "test" : "input"));

$grid = array_map("mb_str_split", explode("\n", trim($input)));

$sGrid = [];
foreach ($grid as $y => $row) {
    foreach ($row as $x => $col) {
        if (!isset($chars[$grid[$y][$x]])) {
            continue;
        }
        foreach ($chars[$grid[$y][$x]] as $word => $letters) {
            foreach ([ [0,1],[0,-1],[1,0],[-1,0],[1,1],[-1,-1],[1,-1],[-1,1] ] as $dir) {
                foreach ($letters as $i => $letter) {
                    if (!isset($grid[$y+$dir[0]*$i][$x+$dir[1]*$i]) || $grid[$y+$dir[0]*$i][$x+$dir[1]*$i] !== $letter) {
                        continue 2; # next direction
                    }
                }
                echo "Found $word at $y,$x\n";
                unset($chars[$letters[0]][$word]);
                if (empty($chars[$letters[0]])) {
                    unset($chars[$letters[0]]);
                }
                unset($words[$word]);
            }
        }
    }
}
ksort($words);
echo "Not found: " . implode(",", array_keys($words)) . "\n";
