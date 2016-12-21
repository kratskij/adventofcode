<?php

$test = false;

$file = ($test) ? "../test.txt" : "input";
$input = explode("\n", trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file)));

$input = array_reverse($input);
$regex = "//";

$values = [];

$pw = "fbgdceah";
if ($test) {
	$pw = "decab";
}

$prevPw = "";
foreach ($input as $row) {
	if ($prevPw == $pw) {
		echo "NOTHING DONE!\n";
		#die();
	}
	$prevPw = $pw;
	echo $row."\n    $pw    ";
	$arr = str_split($pw);
	$w = explode(" ", $row);
	switch($w[0]) {
		case "swap":
			#position X with position Y means that the letters at indexes X and Y (counting from 0) should be swapped.
			if ($w[1] == "position") {
				$h = $pw[$w[2]];
				$pw[$w[2]] = $pw[$w[5]];
				$pw[$w[5]] = $h;
			} else  if ($w[1] == "letter"){
			#letter X with letter Y means that the letters X and Y should be swapped (regardless of where they appear in the string).
				$pw = str_replace($w[2], "[PLACEHOLDER]", $pw);
				$pw = str_replace($w[5], $w[2], $pw);
				$pw = str_replace("[PLACEHOLDER]", $w[5], $pw);
			} else {
				echo "fu";
			}
			break;
		case "rotate":
			#based on position of letter X means that the whole string should be rotated to the right based on the index of letter X (counting from 0) as determined before this instruction does any rotations. Once the index is determined, rotate the string to the right one time, plus a number of times equal to that index, plus one additional time if the index was at least 4.
			if ($w[1] == "based") {
				#$pos = strpos($pw, $w[6]);
				#$nr = ($pos >= 4) ? $pos + 1 : $pos;
				#echo $nr."\n";
				$c = 0;
				$alternatives = [];
				while(true) {
					array_push($arr, array_shift($arr));
					$pw = implode("", $arr);
					$arr = str_split($pw);

					$c++;

					$pos = strpos($pw, $w[6]);
					$nr = ($pos >= 4) ? $pos + 2 : $pos + 1;
					if ($nr == $c) {
						break;
						$alternatives[] = $pw;
					}
					if ($c > strlen($pw)) {
						break;
					}
				}
				#echo count($alternatives) . "ALTERNATIVES";
				#var_dump($alternatives);
			} else {
			#left/right X steps means that the whole string should be rotated;
			#for example, one right rotation would turn abcd into dabc.
				for ($i = 0; $i < $w[2]; $i++) {
					if ($w[1] == "left") {
						$el = array_pop($arr);
						array_unshift($arr, $el);
					} else {
						$el = array_shift($arr);
						array_push($arr, $el);
					}
				}
				$pw = implode("", $arr);
				$arr = str_split($pw);
			}
			break;
		case "reverse":
			#positions X through Y means that the span of letters at indexes X through Y (including the letters at X and Y) should be reversed in order.
			$x = $w[2];
			$y = $w[4];
			$begin = array_slice($arr, 0, $x);
			$slice = array_slice($arr, $x, $y - $x + 1);
			$end = array_slice($arr, $y + 1);
			$slice = array_reverse($slice);
			#var_dump($begin, $slice, $end);
			$arr = array_merge($begin, $slice, $end);
			$pw = implode("", $arr);
			#$begin . strrev(implode("", $slice)) . $arr;
			break;
		case "move":
			#position X to position Y means that the letter which is at index X should be removed from the string, then inserted such that it ends up at index Y.
			$y = $w[2];
			$x = $w[5];
			if ($x < $y) {
				$begin = substr($pw, 0, $x);
				$letter = substr($pw, $x, 1);
				$mid = substr($pw, $x + 1, $y - $x);
				$end = substr($pw, $y + 1);
				$pw = $begin.$mid.$letter.$end;
			} else {
				$begin = substr($pw, 0, $y);
				$mid = substr($pw, $y, $x - $y);
				$letter = substr($pw, $x, 1);
				$end = substr($pw, $x + 1);
				$pw = $begin.$letter.$mid.$end;
			}
			break;
		default:
			echo "fu2";
	}

	echo $pw . "\n";

}
