<?php

$test = false;

$file = ($test) ? "../test.txt" : "input.txt";
$input = preg_replace("/\s/", "", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));
#$input = "(4x14)JVWV(84x11)(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(44x15)(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL";
$regex = "/\(\d+x\d+\)/";
#JVWVJVWVJVWVJVWVJVWVJVWVJVWVJVWVJVWVJVWVJVWVJVWVJVWVJVWV(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(25x11)(1x1)J(12x14)CNZKOSNAJVYL(16x3)QADCLDFUVLLZZYKX(24x2)YAFPPYWOQJKUKQTJACJAOWYF(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)J(12x14)CNZKOSNAJVYL(1x1)JCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLCNZKOSNAJVYLQADCLDFUVLLZZYKXQADCLDFUVLLZZYKXQADCLDFUVLLZZYKX(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(16x4)(1x15)S(4x3)TJLZ(5x13)XVUCL(6x6)SUAXJM(1x15)S(4x3)TJLZ(1x15)S(4x3)TJLZ(1x15)S(4x3)TJLZ(1x15)STJLZTJLZTJLZXVUCLXVUCLXVUCLXVUCLXVUCLXVUCLXVUCLXVUCLXVUCLXVUCLXVUCLXVUCLXVUCL

$values = [];
echo "original input length: " . strlen($input) . "\n";
#echo $input."\n";
$count = 0;
for ($k = 0; $k < strlen($input); $k++) {
	$i = $input[$k];
	$first = $second = "";
	if ($i == "(") {
		$x = 1;
		while(true) {
			if ($input[$k+$x] == "x") {
				break;
			}
			$first .= $input[$k+$x];
			$x++;
		}
		$x++;
		while(true) {
			if ($input[$k+$x] == ")") {
				break;
			}
			$second .= $input[$k+$x];
			$x++;
		}

		$charlen = strlen("(".$first."x".$second.")");
		#echo "NEW: " . "(".$first."x".$second.")"."\n";
		$insert = substr($input, $k + $charlen, (int)$first);
		$firstArr = substr($input, 0, $k);
		$secondArr = substr($input, $k + $charlen + strlen($insert));
		$combined = "";
		for($num = 0; $num < (int)$second; $num++) {
			$combined .= $insert;
		}
		#var_dump($insert, $firstArr, $secondArr, $combined);
		#echo "\n";
#		echo $combined."\n"; sleep(1);
		$input = $firstArr . $combined . $secondArr;
		$k += strlen($combined) - 1;
		$count += strlen($combined) - strlen($insert) - $charlen;
	} else {
		echo $i."\n";
		$count++;
	}
}
$input = preg_replace("/\s/", "", $input);
echo $input."\n";
echo $count."\n";
echo "ending input length: " . strlen($input)."\n";

/*
preg_match_all($regex, $input, $matches);
$matches = $matches[0];

$c = strlen($input);
foreach ($matches as $match) {
	preg_match("/\((\d+)x(\d+)\)/", $match, $m);
	array_shift($m);
	$c += (int)$m[0]*(int)$m[1] - strlen($match);
}

echo $c;
*/

#var_dump($value);
