<?php

class InputReader {

    private $rawData;

    public function __construct($file) {
        $this->rawData = trim(file_get_contents($file));
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
}
