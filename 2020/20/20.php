<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$jurassicJigsaw = new JurassicJigsaw($ir->lines());

echo sprintf(
    "P1: %s\nP2: %s\n",
    array_product($jurassicJigsaw->getCornerTileIds()),
    $jurassicJigsaw->getRoughness()
);

class JurassicJigsaw {
    private $_monster = "                  # \n" .
                        "#    ##    ##    ###\n" .
                        " #  #  #  #  #  #   ";

    private $_tiles = [];
    private $_topLeftCandidates = [];
    private $_image;

    public function __construct($input) {
        $this->parseInput($input);

        $candidates = $this->createCandidates();

        $this->_image = self::findMonsterImage($candidates, $this->_monster);
    }

    public function getCornerTileIds() {
        return array_unique(array_map("reset", $this->_topLeftCandidates));
    }

    private function parseInput($input) {
        foreach ($input as $line) {
            if (strpos($line, "Tile") !== false) {
                $id = (int)explode(":", explode(" ", $line)[1])[0];
                $y = 0;
                continue;
            }
            if (empty($line)) {
                continue;
            }
            $this->_tiles[$id][$y] = $line;
            $y++;
        }
    }

    private function createCandidates() {
        $tileSize = count(reset($this->_tiles));
        $bigImgWidth = sqrt(count($this->_tiles));

        $variations = [];
        foreach ($this->_tiles as $id => $tile) {
            $variations[$id] = rotateflip($tile, $tileSize);
        }

        foreach ($variations as $id1 => $var1) {
            foreach ($variations as $id2 => $var2) {
                if ($id2 == $id1) {
                    continue;
                }
                foreach ($var1 as $rot1 => $tiles1) {
                    $left1 = $right1 = "";
                    foreach ($tiles1 as $row) {
                        $left1 .= $row[0];
                        $right1 .= $row[$tileSize-1];
                    }
                    foreach ($var2 as $rot2 => $tiles2) {
                        $left2 = $right2 = "";
                        foreach ($tiles2 as $row) {
                            $left2 .= $row[0];
                            $right2 .= $row[$tileSize-1];
                        }
                        if ($tiles1[$tileSize-1] == $tiles2[0]) {
                            $top2bottom[$id1][$rot1][$id2][$rot2] = true;
                        }
                        if ($tiles1[0] == $tiles2[$tileSize-1]) {
                            $bottom2top[$id1][$rot1][$id2][$rot2] = true;
                        }
                        if ($left1 == $right2) {
                            $right2left[$id1][$rot1][$id2][$rot2] = true;
                        }
                        if ($right1 == $left2) {
                            $left2right[$id1][$rot1][$id2][$rot2] = true;
                        }
                    }
                }
            }
        }

        foreach ($variations as $id => $vars) {
            foreach ($vars as $rot => $tile) {
                if (
                    !isset($bottom2top[$id][$rot]) && !isset($right2left[$id][$rot]) &&
                    isset($top2bottom[$id][$rot]) && isset($left2right[$id][$rot])
                ) {
                    $this->_topLeftCandidates[] = [$id, $rot];
                }
            }
        }

        $bigImgs = [];
        foreach ($this->_topLeftCandidates as $cand) {
            $tileGrid = [ 0 => [ 0 => $cand ]];
            for ($y = 0; $y < $bigImgWidth; $y++) {
                for ($x = 0; $x < $bigImgWidth; $x++) {
                    if ($x > 0) {
                        $lefty = $tileGrid[$y][$x-1];
                        foreach ($left2right[$lefty[0]][$lefty[1]] as $candidateId => $candidateInfo) {
                            foreach ($candidateInfo as $candidateRot => $none) {
                                $tileGrid[$y][$x] = [ $candidateId, $candidateRot ];
                            }
                        }
                    } else if ($y > 0) {
                        $toppy = $tileGrid[$y-1][$x];
                        foreach ($top2bottom[$toppy[0]][$toppy[1]] as $candidateId => $candidateInfo) {
                            foreach ($candidateInfo as $candidateRot => $none) {
                                $tileGrid[$y][$x] = [ $candidateId, $candidateRot ];
                            }
                        }
                    }
                }
            }

            $bigImg = [];
            foreach ($tileGrid as $y => $bigRow) {
                foreach ($bigRow as $tileInfo) {
                    list($id, $rot) = $tileInfo;
                    foreach ($variations[$id][$rot] as $tileY => $row) {
                        if ($tileY == 0 || $tileY == $tileSize - 1) {
                            continue;
                        }
                        $bigY = ($y * $tileSize) + $tileY;
                        if (!isset($bigImg[$bigY])) {
                            $bigImg[$bigY] = "";
                        }
                        $bigImg[$bigY] .= substr($row, 1, -1);
                    }
                }
            }

            $bigImgs[] = array_values($bigImg);
        }
        return $bigImgs;
    }

    private static function findMonsterImage($images, $monster) {
        $monster = explode("\n", $monster);
        $imageSize = count($images[0]);
        $monsterLength = strlen($monster[0]);
        foreach ($images as $img) {
            $monsterFound = false;

            foreach ($img as $y => $row) {
                if (isset($img[$y+2])) {
                    for ($i = 0; $i <= $imageSize - $monsterLength; $i++) {
                        if (
                            preg_match('/^' . str_replace(' ', '.', $monster[0]) . '$/', substr($img[$y+0], $i, $monsterLength)) == 1 &&
                            preg_match('/^' . str_replace(' ', '.', $monster[1]) . '$/', substr($img[$y+1], $i, $monsterLength)) == 1 &&
                            preg_match('/^' . str_replace(' ', '.', $monster[2]) . '$/', substr($img[$y+2], $i, $monsterLength)) == 1
                        ) {
                            for ($x = 0; $x < $monsterLength; $x++) {
                                foreach ($monster as $offset => $monsterRow) {
                                    if ($monsterRow[$x] == "#") {
                                        $img[$y+$offset][$i+$x] = "O";
                                    }
                                }
                            }
                            $monsterFound = true;
                        }
                    }
                }
            }

            if ($monsterFound) {
                return $img;
            }
        }

        return null;
    }

    public function getRoughness() {
        $roughness = 0;
        foreach ($this->_image as $row) {
            $roughness += substr_count($row, "#");
        }
        return $roughness;
    }
}

function rotate($img, $n, $num = 1) {
    $rows = $img;
    foreach ($rows as &$r) {
        $r = str_split($r);
    }
    $n = count($rows);

    for ($x = 0; $x < $num; $x++) {
        $rotated = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $rotated[$i][$j] = $rows[$n-$j-1][$i];
            }
        }
        $rows = $rotated;
    }

    foreach ($rows as &$r) {
        $r = implode("", $r);
    }

    return $rows;
}

function rotateflip($img, $n)
{
    $return = [];

    $return[] = $img;
    $return[] = rotate($img, $n);
    $return[] = rotate($img, $n, 2);
    $return[] = rotate($img, $n, 3);

    $img = array_map("strrev", $img);;

    $return[] = $img;
    $return[] = rotate($img, $n);
    $return[] = rotate($img, $n, 2);
    $return[] = rotate($img, $n, 3);

    return $return;
}
