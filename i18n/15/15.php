<?php

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$startTime = "083000";
$endTime = "170000";

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);

$input = $ir->lines();

$offices = [];
while ($line = array_shift($input)) {
    [$location, $tz, $holidays] = explode("\t", $line);
    $holidays = explode(";", $holidays);
    $tz = new \DateTimeZone($tz);
    foreach ($holidays as $k => $holiday) {
        $holidayStart = new DateTime($holiday, $tz);
        $holidayEnd = clone $holidayStart;
        $holidayEnd->add(new DateInterval("P1D"))->sub(new DateInterval("PT1S"));

        $holidays[$k] = [
            (int)$holidayStart->format("U"),
            (int)$holidayEnd->format("U")
        ];
    }

    $offices[] = [$tz, $holidays];
}

$customerHolidays = [];
while ($line = array_shift($input)) {
    [$name, $tz, $holidays] = explode("\t", $line);
    $holidays = explode(";", $holidays);
    $tz = new \DateTimeZone($tz);
    foreach ($holidays as $k => $holiday) {
        $holidayStart = new DateTime($holiday, $tz);
        $holidayEnd = clone $holidayStart;
        $holidayEnd->add(new DateInterval("P1D"))->sub(new DateInterval("PT1S"));

        $holidays[$k] = [
            (int)$holidayStart->format("U"),
            (int)$holidayEnd->format("U")
        ];
    }
    $customerHolidays[] = [$name, $tz, $holidays];
}


$start = (new DateTime("2022-01-01 00:00:00", new DateTimeZone("UTC")))->format("U");
$end = (new DateTime("2022-12-31 23:59:59", new DateTimeZone("UTC")))->format("U");

$t = $start - 30*60;
$ans = [];
while ($t <= $end) {
    $t += 30*60;
    $otRequired = true;
    $needsOpenOffice = false;
    $reqs = [];
    foreach ($customerHolidays as $holidays) {
        [$req, $customerTz, $holidays] = $holidays;
        foreach ($holidays as $holiday) {
            if ($t >= $holiday[0] && $t < $holiday[1]) {
                continue 2;
            }
        }
        $d = new \DateTime("@$t");
        $d->setTimezone($customerTz);
        $weekday = $d->format("l");
        if ($weekday == "Saturday" || $weekday == "Sunday") {
            continue;
        }

        $needsOpenOffice = true;
        $reqs[] = $req;
    }

    if (!$needsOpenOffice) {
        continue;
    }

    $hasOpenOffice = false;
    foreach ($offices as $office) {
        [$officeTz, $holidays] = $office;

        $isHoliday = false;
        foreach ($holidays as $holiday) {
            if ($t >= $holiday[0] && $t < $holiday[1]) {
                continue 2;
            }
        }

        $d = new \DateTime("@$t");
        $d->setTimezone($officeTz);

        $weekday = $d->format("l");
        if ($weekday == "Saturday" || $weekday == "Sunday") {
            continue;
        }

        if ($d->format("His") < $startTime || $d->format("His") >= $endTime) {
            continue;
        }

        $hasOpenOffice = true;
        break;
    }

    if (!$hasOpenOffice && $needsOpenOffice) {
        foreach ($reqs as $req) {
            if (!isset($ans[$req])) {
                $ans[$req] = 0;
            }
            $ans[$req] += 30;
        }
    }
}

$ans = max($ans) - min($ans);

echo "Answer: $ans\n";
