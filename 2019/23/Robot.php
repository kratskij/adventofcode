<?php

require_once(__DIR__ . '/../IntCodeComputer.php');

class Robot extends IntCodeComputer {
    public function __construct($code, $ip) {
        parent::__construct($code);
        $this->in($ip, false);
    }

    public function read($inx, $iny) {
        $out = [];

        $this->_lastOutput = false;
        $o = $this->in($inx, false);
        if ($o !== false && $o !== null) {
            $out[] = $o;
        }

        $this->_lastOutput = false;
        $o = $this->in($iny, false);
        if ($o !== false && $o !== null) {
            $out[] = $o;
        }

        while (count($out) < 3) {
            $this->_lastOutput = false;
            $o = $this->in(-1, true, true);
            if ($o !== false && $o !== null) {
                $out[] = $o;
            }
        }

        return $out;
    }
}
