<?php


require_once(__DIR__ . "/tools/Table.php");
require_once(__DIR__ . "/tools/Time.php");

Table::hideEmpty();

if (isset($argv)) {
    $params = $argv[1] ?? ["0"];
    $web = false;
} else {
    $params = explode("/", ($_GET ? array_keys($_GET)[0] : "0"));
    $web = true;
}

$year = $day = 0;
$override = $torunn = false;

$rootUrl = "?";
foreach ($params as $p) {
    if ((int)$p >= 2015 && (int)$p < 2100) {
        $year = (int)$p;
    }
    if ((int)$p >= 1 && (int)$p <= 25) {
        $day = (int)$p;
    }
    if ($p == "override") {
        $override = true;
        $rootUrl .= "override/";
    }
    if ($p == "torunn") {
        $torunn = true;
    }
}

if ($year == 0) {
    $year = date("Y");
};

$leaderBoardId = 116603;
$cookie = "session=53616c7465645f5fc3434e6597ddcc1fcf3e06f7f1009dc5d3a279e85e7be6783063caf602bfa890bcbf99317d699238";
$refreshInterval = ($year == date("Y")) ? 900 : 86400;

$ignoredDays = [ // days ignored due to outage or other problems
    2020 => [1 => true],
];

$startingTimeOverrides = [];
if ($override) {
    $startingTimeOverrides = [
        "kratskij" => [
            2021 => [
                1 => "06:39:37",
                2 => "05:58:54",
                3 => "07:11:37",
                4 => "09:24:58",
                5 => "10:09:31",
                6 => "06:45:51",
                7 => "06:52:39",
                8 => "06:02:33",
                9 => "06:10:54",
                10 => "06:13:05",
                11 => "11:54:45",
                12 => "09:21:30",
                13 => "06:58:39",
                14 => "07:40:45",
                15 => "06:11:32",
                16 => "06:00:05",
                17 => "09:21:53",
                18 => "14:45:10",
                19 => "10:08:33",
                20 => "09:20:37",
                21 => "05:00:00",
                22 => "12:11:39",
                23 => "06:17:51",
                24 => "08:11:49",
                25 => "11:18:06",
            ],
        ]
    ];
}

if ($torunn) {
    $ignoredDays[2020][20] = true;
}

$now = time();
$updatedUts = ($now - ($now % $refreshInterval));
$filename = "/tmp/aoc_leaderboard-$year-" . $updatedUts;


if (!file_exists($filename)) {
    $ch = curl_init ("https://adventofcode.com/{$year}/leaderboard/private/view/{$leaderBoardId}.json");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: {$cookie}"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($ch);
    file_put_contents($filename, $json);
    curl_close($ch);
    file_put_contents($filename."-uts", $now);
}

$updatedUts = file_get_contents($filename."-uts");

$json = json_decode(file_get_contents($filename), true);
if (!$json) {
    die ("Could not parse json.");
}

foreach ($json["members"] as $id => $member) {
    if (!$member["name"]) {
        $json["members"][$id]["name"] = "Anon $id";
    }
}

if (is_null($json)) {
    die("Cookie expired?");
}

$links = [];
$lastStars = [];
if ($day == 0) {
    $yearLink = ($year == date("Y")) ? "" : "/$year";
    $members = [];
    for ($i = 1; $i <= 25; $i++) {
        if (isset($ignoredDays[$year][$i])) {
            $links["{$rootUrl}{$i}{$yearLink}"] = "$i";
            // skipped due to outage
            continue;
        }
        foreach (getMembersByDay($json, $year, $i, $startingTimeOverrides) as $member) {
            if ($member["star1seconds"]) {
                #echo $i;
                $links["{$rootUrl}{$i}{$yearLink}"] = "$i";
            }
            $starString = ($member["star2seconds"] ? "★" : ($member["star1seconds"] ? "☆" : " "));
            $secondsSpent = ($member["star2seconds"]) ? $member["star2seconds"] + $member["star1seconds"] : $member["star1seconds"];
            if (isset($members[$member["name"]])) {
                $members[$member["name"]]["star1seconds"] += $member["star1seconds"];
                $members[$member["name"]]["star2seconds"] += $member["star2seconds"];
                $members[$member["name"]]["points"] += $member["points"];
                $members[$member["name"]]["star1Count"] += $member["star1seconds"] ? 1 : 0;
                $members[$member["name"]]["star2Count"] += $member["star2seconds"] ? 1 : 0;
                $members[$member["name"]]["starstring"] .= $starString;
                $members[$member["name"]]["secondsSpent"] += $secondsSpent;
                $members[$member["name"]]["delayedStart"] += $member["delayedStart"];
            } else {
                $members[$member["name"]] = $member;
                $members[$member["name"]]["star1Count"] = $member["star1seconds"] ? 1 : 0;
                $members[$member["name"]]["star2Count"] = $member["star2seconds"] ? 1 : 0;
                $members[$member["name"]]["starstring"] = $starString;
                $members[$member["name"]]["secondsSpent"] = $secondsSpent;
                $members[$member["name"]]["delayedStart"] = $member["delayedStart"];
            }
        }
    }

    $members = array_filter($members, function($member) {
        return $member["star1Count"] > 0;
    });

    foreach ($members as &$member) {
        $member = [
            "name" => $member["name"] . (($member["delayedStart"]) ? " (-" . Time::humanDuration(floor($member["delayedStart"] / $member["star1Count"])) . ")" : ""),
            "star1avg" => Time::humanDuration(floor($member["star1seconds"] / $member["star1Count"])),
            "star2avg" => Time::humanDuration(floor($member["star2seconds"] / ($member["star2Count"] ?: 1))),
            "totalavg" => Time::humanDuration(floor(($member["star1seconds"] + $member["star2seconds"]) / ($member["star1Count"]))),
            "timeSpent" => Time::humanDuration(floor($member["secondsSpent"])),
            "stars" => rtrim($member["starstring"]),
            "points" => $member["points"],
            "globalScore" => $member["globalScore"]
        ];
        $columnSettings = [
            "star1avg" => [ "padDir" => STR_PAD_LEFT ],
            "star2avg" => [ "padDir" => STR_PAD_LEFT ],
            "totalavg" => [ "padDir" => STR_PAD_LEFT ],
            "timeSpent" => [ "padDir" => STR_PAD_LEFT ],
            "points" => [ "padDir" => STR_PAD_LEFT ],
            "globalScore" => ["padDir" => STR_PAD_LEFT ],
        ];
    }
    uasort($members, function($a, $b) {
        if ($a["points"] == $b["points"]) {
            if ($a["stars"] == $b["stars"]) {
                return strcmp($a["totalavg"], $b["totalavg"]);
            }
            return $a["stars"] > $b["stars"] ? -1 : 1;
        }
        return $a["points"] > $b["points"] ? -1 : 1;
    });

    $lastStars = getLastStars($json, 10);
    foreach ($lastStars as $k => $star) {
        $lastStars[$k]["when"] = Time::humanDuration($now - $star["uts"]) . " ago";
        unset($lastStars[$k]["uts"]);
    }
} else {
    if ($year == date("Y")) {
        $links[$rootUrl] = "$year Overview";
    } else {
        $links["{$rootUrl}0/$year"] = "$year Overview";
    }
    $members = getMembersByDay($json, $year, $day, $startingTimeOverrides);

    $members = array_filter($members, function($member) {
        return $member["star1seconds"] > 0;
    });

    foreach ($members as &$member) {
        $member = [
            "name" => $member["name"] . ($member["delayedStart"] ? " (-" . Time::humanDuration($member["delayedStart"]) . ")" : ""),
            "star1" => Time::humanDuration($member["star1seconds"]) ?: NULL,
            "star2" => Time::humanDuration(floor($member["star2seconds"])) ?: NULL,
            "total" => ($member["star2seconds"]) ? Time::humanDuration(floor(($member["star1seconds"] + $member["star2seconds"]))) : NULL,
            "points" => $member["points"],
        ];
    }
    $columnSettings = [
        "star1" => [ "padDir" => STR_PAD_LEFT ],
        "star2" => [ "padDir" => STR_PAD_LEFT ],
        "total" => [ "padDir" => STR_PAD_LEFT ],
        "points" => [
            "padDir" => STR_PAD_LEFT,
            "style" => (isset($ignoredDays[$year][$day]) ? "text-decoration: line-through;" : "")
        ],
    ];
    uasort($members, function($a, $b) {
        if ($a["points"] == $b["points"]) {
            return strcmp($a["total"], $b["total"]);
        }
        return $a["points"] > $b["points"] ? -1 : 1;
    });
}


if ($web) {
    if ($day) {
        echo "<html><body><h1>Advent of Code $year - Day $day</h1>";
    } else {
        echo "<h1>Advent of Code $year - Overview</h1>";
    }
    foreach ($links as $url => $name) {
        echo "<a href='$url'>$name</a> ";
    }
    echo "<br />Updated at " . date("H:i:s", $updatedUts) . " UTC";
    echo "<pre>\n";
    Table::print($members, $columnSettings);
    echo "</pre>";

    if (!$day) {
        echo "<h2>Last stars</h2>\n";
        echo "<pre>\n";
        Table::print($lastStars);
        echo "</pre>\n";
    }

    echo "<br /><br />Points formula:<br />
        Star 1: <span style='font-family:monospace'>&lt;star 1 completionists&gt; - &lt;star1 placement&gt; + 1</span><br />
        Star 2 (only if star 2 is completed): <span style='font-family:monospace'>&lt;total completionists&gt; - &lt;total placement&gt; + 1</span><br />
        Points: <span style='font-family:monospace'>Star1 + Star 2</span><br /></body></html>";
} else {
    Table::print($members, $columnSettings);
}


function getLastStars(&$json, $limit) {
    $lastStars = [];
    foreach ($json["members"] as $member) {
        foreach ($member["completion_day_level"] as $day => $stars) {
            foreach ($stars as $star => $s) {
                $lastStars[] = [
                    "uts" => (int)$s["get_star_ts"],
                    "name" => $member["name"],
                    "day" => $day,
                    "star" => $star
                ];
            }
        }
    }

    usort($lastStars, function($a, $b) {
        return $b["uts"] - $a["uts"];
    });

    return array_slice($lastStars, 0, $limit);
}

function getMembersByDay(&$json, $year, $day, &$startingTimeOverrides) {
    $members = [];
    $opensAtUts = strtotime("$year-12-" . str_pad($day, 2, "0", STR_PAD_LEFT) . "05:00:00");
    foreach ($json["members"] as $member) {
        if (!isset($member["completion_day_level"][$day])) {
            #continue;
        }
        $stars = $member["completion_day_level"][$day] ?? null;

        if (isset($startingTimeOverrides[$member["name"]][$year][$day])) {
            $fakeOpensAtUts = strtotime("$year-12-$day " . $startingTimeOverrides[$member["name"]][$year][$day]);
        } else {
            $fakeOpensAtUts = $opensAtUts;
        }

        $members[$member["name"]] = [
            "name" => $member["name"],
            "star1seconds" => isset($stars[1]["get_star_ts"]) ? $stars[1]["get_star_ts"] - $fakeOpensAtUts : null,
            "star2seconds" => isset($stars[2]["get_star_ts"]) ? $stars[2]["get_star_ts"] - $stars[1]["get_star_ts"] : null,
            "points" => 0,
            "globalScore" => $member["global_score"] ?: null,
            "stars" => [
                1 => [
                    "uts" => (isset($stars[1]["get_star_ts"]) ? $stars[1]["get_star_ts"] : null),
                    "fakeUts" => (isset($stars[1]["get_star_ts"]) ? $stars[1]["get_star_ts"] - ($fakeOpensAtUts - $opensAtUts) : null),
                    "points" => 0,
                ],
                2 => [
                    "uts" => (isset($stars[2]["get_star_ts"]) ? $stars[2]["get_star_ts"] : null),
                    "fakeUts" => (isset($stars[2]["get_star_ts"]) ? $stars[2]["get_star_ts"] - ($fakeOpensAtUts - $opensAtUts) : null),
                    "points" => 0,
                ]
            ],
            "delayedStart" => $fakeOpensAtUts - $opensAtUts,
        ];
    }

    assignPoints($members, 1);
    assignPoints($members, 2);

    foreach ($members as &$member) {
        $member["points"] = $member["stars"][1]["points"] + $member["stars"][2]["points"];
    }

    return $members;
}

function assignPoints(&$members, $star) {
    $membersCopy = $members;
    uasort($membersCopy, function($a, $b) use ($star) {
        if (is_null($a["stars"][$star]["uts"])) { return -1; }
        if (is_null($b["stars"][$star]["uts"])) { return 1; }
        if ($a["stars"][$star]["fakeUts"] == $b["stars"][$star]["fakeUts"]) { return 0; }
        return $a["stars"][$star]["fakeUts"] > $b["stars"][$star]["fakeUts"] ? 1 : -1;
        #if ($a["stars"][$star]["uts"] == $b["stars"][$star]["uts"]) { return 0; }
        #return $a["stars"][$star]["uts"] > $b["stars"][$star]["uts"] ? 1 : -1;
    });
    $points = count($membersCopy);
    $prevUts = false;
    foreach ($membersCopy as $key => $info) {
        if ($info["stars"][$star]["uts"] !== $prevUts) {
            $prevPoints = $points;
            $prevUts = $info["stars"][$star]["uts"];
        }
        $members[$key]["stars"][$star]["points"] = ($prevUts ? $prevPoints : 0);
        $points--;
    }

}
