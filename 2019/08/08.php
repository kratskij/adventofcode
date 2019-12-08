<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");
require_once(__DIR__."/../Util.php");

$ir = new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file);
$input = $ir->trim(true)->chars();
$input = array_map("intval", $input);

$image = new Image($input, 25, 6);

echo "Part 1: " . $image->findMinCount(0, [1,2]) . "\n";
echo "Part 2:\n"; $image->render();

class Image {
    private $_layers;

    public function __construct(array $data, $width, $height) {
        $pixels = $width * $height;
        foreach ($data as $key => $val) {
            $layerIdx = floor($key / $pixels);
            $rem = $key % $pixels;
            $row = floor($rem / $width);
            $col = $rem % $width;
            $this->_layers[$layerIdx][$row][$col] = $val;
        }
    }

    public function findMinCount($fewestCount, array $multipliers) {
        $layerCounts = [];
        foreach ($this->_layers as $idx => $layer) {
            foreach ($layer as $row) {
                foreach ($row as $val) {
                    @$layerCounts[$idx][$val]++;
                }
            }
        }

        $minCountIdx = false;
        foreach ($layerCounts as $idx => $values) {
            if ($minCountIdx === false || $values[$fewestCount] < $layerCounts[$minCountIdx][0]) {
                $minCountIdx = $idx;
            }
        }

        $ret = 1;
        foreach ($multipliers as $multiplier) {
            $ret *= $layerCounts[$minCountIdx][$multiplier];
        }

        return $ret;
    }

    public function render() {
        $result = [];
        foreach ($this->_layers[0] as $rowNum => $row) {
            foreach ($row as $colNum => $val) {
                $idx = 0;
                while ($val != 0 && $val != 1) {
                    $val = $this->_layers[++$idx][$rowNum][$colNum];
                }
                echo ($val == 1) ? "#" : " ";
            }
            echo "\n";
        }
    }
}
