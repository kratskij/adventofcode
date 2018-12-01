<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";

$file = ($test) ? "test" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$grid = [];

foreach ($input as $k => $line) { #$matches?
    $grid[] = str_split($line);
}

$rules = [
    "Part 1" => [
        "rules" => [
            "." => [-90, "#"],
            "#" => [90, "."],
        ],
        "stop" => 10000
    ],
    "Part 2" => [
        "rules" => [
            "." => [-90, "W"],
            "W" => [0, "#"],
            "#" => [90, "F"],
            "F" => [180, "."]
        ],
        "stop" => ($test) ? 3 : 10000000
    ],
];


foreach ($rules as $part => $setup) {
    echo "$part: " . (new Virus($grid, $setup["rules"], Virus::UP))->spread($setup["stop"]) . "\n";

    Virus::$visualize = true;
}

class Virus
{
    const UP = 0;
    const RIGHT = 1;
    const DOWN = 2;
    const LEFT = 3;

    private $_grid;
    private $_rules;

    public static $visualize = false;

    public function __construct($g, $rules, $direction) {
        $this->_grid = $g;
        $this->_rules = $rules;
        $this->_direction = $direction;
    }

    public function spread($stopAt) {
        $this->_direction = $direction;
        $y = $x = floor(count($this->_grid) / 2);
        $infected = 0;

        for ($i = 0; $i < $stopAt; $i++) {
            $rule = $this->_rules[$this->getValue($y, $x)];
            $this->_direction -= $rule[0] / 90;
            if ($this->_direction < 0) {
                $this->_direction = 3;
            } else if ($this->_direction > 3) {
                $this->_direction = 0;
            }
            if ($rule[1] == "#") {
                $infected++;
            }
            $this->_grid[$y][$x] = $rule[1];

            #p($g, $this->_direction, $y, $x, $infected, $i);

            switch ($this->_direction) {
                case self::UP: $y--; break;
                case self::DOWN: $y++; break;
                case self::LEFT: $x--; break;
                case self::RIGHT: $x++; break;
            }
            if (self::$visualize) {
                $this->print();
            }
        }
        return $infected;
    }

    private function getValue($y, $x)
    {
        if (!isset($this->_grid[$y][$x])) {
            $this->_grid[$y][$x] = ".";
        }

        return $this->_grid[$y][$x];
    }

    private function print()
    {

        $min = min(array_map(function($r) { return min(array_keys($r)); }, $this->_grid));
        $max = max(array_map(function($r) { return max(array_keys($r)); }, $this->_grid));

        $str = "";
        ksort($this->_grid);
        $conv = [
            "#" => "\e[31m█\e[0m",
            "." => "\e[30m█\e[0m",
            "F" => "\e[32m█\e[0m",
            "W" => "\e[33m█\e[0m",
        ];
        foreach ($this->_grid as $k => $r) {
            ksort($r);
            for ($i = $min; $i <= $max; $i++) {
                $str .= isset($r[$i]) ? $conv[$r[$i]] : $conv["."];
            }
            $str .= "\n";
        }
        #$str .= "dir: $dir, y:$y, x:$x\n, min:$min, max:$max\n";
        #$str .= "count: $count, infected: $infected\n";
        system("clear");
        echo $str;
        usleep(1000000);
    }
}



#echo $sum;
#echo $outString;
