<?php

class Util {
    private const JP_NUMERAL_MAPPING = [
        '〇' => 0,
        '零' => 0,
        '一' => 1,
        '壱' => 1,
        '二' => 2,
        '弐' => 2,
        '三' => 3,
        '参' => 3,
        '四' => 4,
        '五' => 5,
        '六' => 6,
        '七' => 7,
        '八' => 8,
        '九' => 9,
    ];

    private const JP_MYRIAD_DIVISIONS_MAPPING = [
        '十' => 10,
        '拾' => 10,
        '百' => 100,
        '千' => 1000,
    ];

    // What power of ten each myriad is
    private const JP_MYRIAD_MAPPING = [
        '万' => '4',
        '萬' => '4',
        '億' => '8',
        '兆' => '12',
        '京' => '16',
        '垓' => '20',
        '秭' => '24',
        '穣' => '28',
        '溝' => '32',
        '澗' => '36',
        '正' => '40',
        '載' => '44',
        '極' => '48',
        '恒河沙' => '52',
        '阿僧祇' => '56',
        '那由他' => '60',
        '不可思議' => '64',
        '無量大数' => '68',
    ];

    private const JP_LENGTHS = [
        "尺" => 10/33, // Shaku () = 10/33 m
        "間" => 6*(10/33), // Ken () = 6 Shaku (尺)
        "丈" => 10*(10/33), // Jo () = 10 Shaku (尺)
        "町" => 360*(10/33), // Cho () = 360 Shaku (尺)
        "里" => 12960*(10/33), // Ri () = 12960 Shaku (尺)
        "毛" => (10/33)/10000, // Mo () = 1 Shaku (尺)
        "厘" => (10/33)/1000, // Rin () = 1 Shaku (尺)
        "分" => (10/33)/100, // Bu () = 1 Shaku (尺)
        "寸" => (10/33)/10, // Sun () = 1 Shaku (尺)
    ];

    public static function printGrid($grid, $minY = PHP_INT_MAX, $maxY = -PHP_INT_MAX, $minX = PHP_INT_MAX, $maxX = -PHP_INT_MAX) {
        #$minY = $minX = $min;
        #$maxY = $maxX = $max;
        foreach ($grid as $y => $row) {
            $minY = min($minY, $y);
            $maxY = max($maxY, $y);
            $minX = min($minX, min(array_keys($row)));
            $maxX = max($maxX, max(array_keys($row)));
        }

        $out = "";
        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                if (!isset($grid[$y][$x])) {
#                    echo "not set at $y,$x\n";
                    $out .= "░";
                } else {
                    if (is_array($grid[$y][$x])) {
#                        var_dump($grid[$y][$x]);
                        if (count($grid[$y][$x]) > 1) {
                            $out .= count($grid[$y][$x]);
                        } else {
                            $out .= $grid[$y][$x][0];
                        }

                    } else {
                        $out .= $grid[$y][$x];
                    }
                }
            }
            $out .= "\n";
        }

        return $out."\n\n";
    }

    public static function printTetris($grid, $rock, $rockY, $rockX) {
        foreach ($rock as $cy => $cxs) {
            foreach ($cxs as $cx) {
                $grid[$rockY+$cy][$rockX+$cx] = false;
            }
        }
        $minY = $minX = PHP_INT_MAX;
        $maxY = $maxX = -PHP_INT_MAX;
        foreach ($grid as $y => $row) {
            $minY = min($minY, $y);
            $maxY = max($maxY, $y);
            $minX = min($minX, min(array_keys($row)));
            $maxX = max($maxX, max(array_keys($row)));
        }

        $out = "";
        for ($y = $minY; $y <= $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                if (!isset($grid[$y][$x])) {
                    $out .= " ";
                } else if ($grid[$y][$x] === true) {
                    $out .= "█";
                } else if ($grid[$y][$x] === false) {
                    $out .= "#";
                }
            }
            $out .= "\n";
        }

        echo $out."\r\n\r\n";
    }

    public static function removeAccents($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;

        $chars = array(
        // Decompositions for Latin-1 Supplement
        chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
        chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
        chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
        chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
        chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
        chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
        chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
        chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
        chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
        chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
        chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
        chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
        chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
        chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
        chr(195).chr(191) => 'y',
        // Decompositions for Latin Extended-A
        chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
        chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
        chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
        chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
        chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
        chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
        chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
        chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
        chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
        chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
        chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
        chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
        chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
        chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
        chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
        chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
        chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
        chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
        chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
        chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
        chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
        chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
        chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
        chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
        chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
        chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
        chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
        chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
        chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
        chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
        chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
        chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
        chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
        chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
        chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
        chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
        chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
        chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
        chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
        chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
        chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
        chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
        chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
        chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
        chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
        chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
        chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
        chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
        chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
        chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
        chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
        chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
        chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
        chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
        chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
        chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
        chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
        chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
        chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
        chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
        chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
        chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
        chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }


    public static function convertJpNumeral(string $number): string
    {
        $result = '0';
        $remainingNumber = $number;

        /**
         * @var array<non-empty-string, numeric-string> $mapping
         */
        $mapping = array_reduce(array_keys(self::JP_MYRIAD_MAPPING), function (array $carry, $item) {
            $carry[$item] = bcpow('10', self::JP_MYRIAD_MAPPING[$item]);
            return $carry;
        }, []);
        arsort($mapping, SORT_NATURAL);

        foreach ($mapping as $key => $value) {
            $check = explode($key, $remainingNumber);
            if (count($check) > 2) {
                throw new InvalidArgumentException('Invalid number format.');
            }
            if (count($check) === 2) {
                $left = $check[0] === '' ? '1' : self::jpConvertDivision($check[0]);
                $result = bcadd($result, bcmul((string)$left, $value));
                $remainingNumber = $check[1];
            }
        }
        if ($remainingNumber !== '') {
            $result = bcadd($result, (string)self::jpConvertDivision($remainingNumber));
        }
        return $result;
    }

    private static function jpConvertDivision(string $number): int
    {
        $result = 0;
        $remainingNumber = $number;

        $mapping = self::JP_MYRIAD_DIVISIONS_MAPPING;
        arsort($mapping);

        foreach ($mapping as $key => $value) {
            $check = explode($key, $remainingNumber);
            if (count($check) > 2) {
                throw new InvalidArgumentException('Invalid number format.');
            }
            if (count($check) === 2) {
                $left = $check[0] === '' ? 1 : self::jpConvertNumeral($check[0]);
                $result += $left * $value;
                $remainingNumber = $check[1];
            }
        }
        if ($remainingNumber !== '') {
            $result += self::jpConvertNumeral($remainingNumber);
        }
        return $result;
    }

    private static function jpConvertNumeral(string $japaneseNumeral): int
    {
        if (!isset(self::JP_NUMERAL_MAPPING[$japaneseNumeral])) {
            throw new InvalidArgumentException('Invalid numeral: ' . $japaneseNumeral);
        }
        return self::JP_NUMERAL_MAPPING[$japaneseNumeral];
    }

    public static function convertJpLength(string $length) {
        return self::JP_LENGTHS[$length];
    }
}
