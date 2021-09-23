<?php

class InputReader {

    private static $_cookie = "session=<YOUR_SESSION_ID>";
    private static $_year = 2020;

    private $rawData;

    public function __construct($file) {
        $this->rawData = file_get_contents($file);
    }
    public function trim($areYouSure = false) {
        $this->rawData = trim($this->rawData);
        if (!$areYouSure) {
            echo "REMINDER: Trimming input data.\n";
        }
        return $this;
    }

    public function raw() {
        return $this->rawData;
    }
    public function lines() {
        return explode("\n", $this->rawData);
    }

    public function explode($char) {
        return explode($char, $this->rawData);
    }

    public function chars() {
        return str_split($this->rawData);
    }
    public function grid($convertables = []) {
        $ret = [];
        foreach ($this->lines() as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                if (isset($convertables[$char])) {
                    $ret[$y][$x] = $convertables[$char];
                } else {
                    $ret[$y][$x] = $char;
                }
            }
        }
        return $ret;
    }

    public function regex($pattern) {
        $return = [];
        foreach ($this->lines() as $line) {
            preg_match("/$pattern/i", $line, $matches);
            array_shift($matches);
            $return[] = $matches;
        }
        return $return;
    }

    public function csv($separator) {
        return array_map(
            function($line) use ($separator) {
                return explode($separator, $line);
            },
            explode("\n", $this->rawData)
        );
    }

    private static function autoDownload($day, $file) {
        $ch = curl_init (
            sprintf("https://adventofcode.com/%d/day/%d/input", self::$_year, (int)($day))
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: " . self::$_cookie));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $inputData = curl_exec($ch);
        file_put_contents($file, $inputData);
        curl_close($ch);

        if (file_exists($file)) {
            echo "File downloaded successfully\n";
            echo "--- Inspecting first lines ---\n";
            $lines = explode("\n", file_get_contents($file));
            $i = 0;
            while ($i < 10 && isset($lines[$i])) {
                $line = $lines[$i];
                if (strlen($line) > 100) {
                    echo substr($line, 0, 100) . " ... [cut due to long line]\n";
                } else {
                    echo "$line\n";
                }
                $i++;
            }
            echo "---\n";
            echo count($lines) . " lines in total\n";
            echo "--- End of inspection ---\n";
            return true;
        } else {
            return false;
        }
    }
}
