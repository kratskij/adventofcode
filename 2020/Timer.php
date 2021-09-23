<?php

class Timer {
    private static $_startTime;
    private static $_runtimes = [];
    public static function start() {
        self::$_startTime = microtime(true);
    }

    public static function restart($name) {
        self::$_runtimes[$name] = (int)((microtime(true) - self::$_startTime) * 1000000);
        self::start();
    }

    public static function out() {
        foreach (self::$_runtimes as $name => $runtime) {
            echo "Time $name:\t$runtime µs\n";
        }
    }
}
