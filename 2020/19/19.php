<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
list($rules, $msgs) = $ir->explode("\n\n");

$rules = explode("\n", $rules);
$msgs = explode("\n", $msgs);

$links = [];
foreach ($rules as $rule) {
    list($parent, $children) = explode(":", trim($rule));
    $children = explode("|", $children);
    foreach ($children as $i => $childr) {
        $childr = explode(" ", trim($childr));
        foreach ($childr as $child) {
            $child = is_numeric($child) ? (int)$child : str_replace('"', '', $child);
            $links[(int)$parent][$i][] = $child;
        }
    }
}

$p1 = count(array_filter($msgs, function($msg) use ($links) { return in_array($msg, isMatch($links, $msg)); }));

$links[8] = [ [42], [42,8]];
$links[11] = [ [42,31], [42,11,31]];

$p2 = count(array_filter($msgs, function($msg) use ($links) { return in_array($msg, isMatch($links, $msg)); }));

function isMatch(&$links, &$msg, $rule = 0, $msgSoFar = "") {
    if (count($links[$rule]) == 1 && count(reset($links[$rule])) == 1) {
        $first = reset($links[$rule]);
        if (!is_numeric(reset($first))) {
            return $first;
        }
    }
    if (substr($msg, 0, strlen($msgSoFar)) !== $msgSoFar) {
        echo "heia\n";
        return [];
    }

    $ret = [];
    foreach ($links[$rule] as $subRuleIdx => $subRules) {
        $newRet = [""];
        foreach ($subRules as $subRule) {
            $nr = [];
            foreach ($newRet as $r) {
                foreach (isMatch($links, $msg, $subRule, substr($msg, 0, strlen($msgSoFar.$r))) as $alternative) {
                    if (strpos($msg, $msgSoFar.$r.$alternative) === false) {
                        continue;
                    }
                    $nr[] = $r.$alternative;
                }
            }
            $newRet = $nr;
        }
        $ret = array_merge($ret, $newRet);
    }
    return $ret;
}

echo "P1: $p1\nP2: $p2\n";
