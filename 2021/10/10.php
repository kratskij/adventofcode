<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$p1 = $p2 = 0;

$map = [
    "(" => ")",
    "[" => "]",
    "{" => "}",
    "<" => ">",
];

$points = [
    ")" => [ 3,      1, ],
    "]" => [ 57,     2, ],
    "}" => [ 1197,   3, ],
    ">" => [ 25137,  4, ],
];
$p2scores = [];

foreach ($input as $line) {
    try {
        findError($line, $map);
    } catch (SyntaxError $e) {
        $p1 += $points[$e->getCharacter()][0];
    } catch (UnclosedBraceError $e) {
        $s = 0;
        foreach ($e->getMissingBraces() as $character) {
            $s *= 5;
            $s += $points[$character][1];
        }
        $p2scores[] = $s;
    }
}

sort($p2scores);
$p2 = $p2scores[floor(count($p2scores) / 2)];

echo "P1: $p1\nP2: $p2\n";

function findError($line, &$map, $base = true) {
    $missing = [];
    foreach (str_split($line) as $n => $c) {
        if (!isset($map[$c])) {
            // it's a closing character!
            // let's look back to find the corresponding opening bracket.

            $a = -1;
            $substr = "";
            $foundOpen = false;
            foreach (str_split(strrev(substr($line, 0, $n))) as $nn => $sc) {
                if ($sc === $c) {
                    $a--;
                } else if (($map[$sc] ?? null) === $c) {
                    $a++;
                }
                if ($a == 0) {
                    // we've found the corresponding opening bracket!
                    unset($missing[$n - $nn - 1]);
                    // let's validate the contents.
                    if ($substr) {
                        try {
                            findError($substr, $map, false);
                        } catch (UnclosedBraceError $e) {
                            // this was just a substring, so the actual error is on the current character
                            throw new SyntaxError("Breaking '$c'", $c);
                        }
                    }
                    continue 2;
                }
                $substr = $sc . $substr;
            }
            throw new SyntaxError("Breaking '$c'", $c);
        } else {
            $missing[$n] = $map[$c];
        }
    }
    if (array_filter($missing)) {
        throw new UnclosedBraceError("Unclosed braces found", array_reverse($missing));
    }
    return false;
}

class SyntaxError extends \Exception {

    private $_character;

    public function __construct($message, $character) {
        $this->_character = $character;
        parent::__construct($message);
    }

    public function getCharacter() {
        return $this->_character;
    }
}

class UnclosedBraceError extends \Exception {

    private $_missing;

    public function __construct($message, $missing) {
        $this->_missing = $missing;
        parent::__construct($message);
    }

    public function getMissingBraces() {
        return $this->_missing;
    }
}
