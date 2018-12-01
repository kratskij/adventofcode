<?php

ini_set('memory_limit','2048M');

func();

function func()
{
    $state = "A";
    $tape = [];
    $cursor = 0;

    $move = function($dir) use (&$tape, &$cursor) {
        if ($dir == "left") {
            $cursor--;
        } else if ($dir == "right") {
            $cursor++;
        }
    };

    for ($i = 0; $i < 12317297; $i++) {
        if (!isset($tape[$cursor])) {
            $tape[$cursor] = 0;
        }
        switch($state) {
            case "A":
                if ($tape[$cursor] == 0) {
                    $tape[$cursor] = 1;
                    $move("right");
                    $state = "B";
                } else if ($tape[$cursor] == 1) {
                    $tape[$cursor] = 0;
                    $move("left");
                    $state = "D";
                }
                break;

            case "B":
                if ($tape[$cursor] == 0) {
                    $tape[$cursor] = 1;
                    $move("right");
                    $state = "C";
                } else if ($tape[$cursor] == 1) {
                    $tape[$cursor] = 0;
                    $move("right");
                    $state = "F";
                }
                break;

            case "C":
                if ($tape[$cursor] == 0) {
                    $tape[$cursor] = 1;
                    $move("left");
                    $state = "C";
                } else if ($tape[$cursor] == 1) {
                    $tape[$cursor] = 1;
                    $move("left");
                    $state = "A";
                }
                break;

            case "D":
                if ($tape[$cursor] == 0) {
                    $tape[$cursor] = 0;
                    $move("left");
                    $state = "E";
                } else if ($tape[$cursor] == 1) {
                    $tape[$cursor] = 1;
                    $move("right");
                    $state = "A";
                }
                break;

            case "E":
                if ($tape[$cursor] == 0) {
                    $tape[$cursor] = 1;
                    $move("left");
                    $state = "A";
                } else if ($tape[$cursor] == 1) {
                    $tape[$cursor] = 0;
                    $move("right");
                    $state = "B";
                }
                break;

            case "F":
                if ($tape[$cursor] == 0) {
                    $tape[$cursor] = 0;
                    $move("right");
                    $state = "C";
                } else if ($tape[$cursor] == 1) {
                    $tape[$cursor] = 0;
                    $move("right");
                    $state = "E";
                }
                break;
        }
    }

    echo count(array_filter($tape)) . "\n";
}
