<?php

ini_set('memory_limit','2048M');

$file = $argv[1] ?? "input";
$test = $file == "test";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->regex("Player\s(\d+) starting position: (\d+)");
list($current, $opponent) = array_map(
    function($line) {
        list($id, $position) = $line;
        return new Player($id, $position);
    },
    $input
);

$universe = Universe::create($current, $opponent);
while ($universe->regularThrow(1000));
$p1 = $universe->getRolls() * Player::getLoser()->getScore();
$universe->reset();

$universe = Universe::create($current, $opponent);
while (Universe::quantumThrowAll(21));
$p2 = max(Player::getWinCounts());

echo "P1: $p1\nP2: $p2\n";

class Player {
    private $_id;
    private $_position;
    private $_score = 0;

    private $_origPosition;
    private static $_winCounts = [];
    private static $_references = [];

    public function __construct($id, $position) {
        $this->_id = $id;
        $this->_position = $position;

        $this->_origPosition = $position;
        self::$_winCounts[$id] = 0;
        self::$_references[$id] = $this;
    }

    public function reset() {
        $this->_position = $this->_origPosition;
        $this->_score = 0;
        self::$_winCounts[$this->_id] = 0;
    }

    public function increasePos($val) {
        $this->_position += $val;
        $this->_score += (($this->_position - 1) % 10) + 1;
    }

    public function getId() {
        return $this->_id;
    }

    public function getScore() {
        return $this->_score;
    }

    public function __toString() {
        return "{$this->_id}:{$this->_position}:{$this->_score}";
    }

    public function setWinner($instanceCount) {
        self::$_winCounts[$this->_id] += $instanceCount;
    }

    public static function getWinCounts() {
        return self::$_winCounts;
    }

    public static function getLoser() {
        return self::$_references[array_search(min(self::$_winCounts), self::$_winCounts)];
    }
}

class Universe {
    private $_instanceCount = 0;

    private $_current;
    private $_opponent;

    private $_rolls = 0;
    private $_nextDie = 1;

    private static $_instances = [];

    public static function create(Player $current, Player $opponent, $instanceCount = 1) {
        $hash = "$current;$opponent";
        if (!isset(self::$_instances[$hash])) {
            self::$_instances[$hash] = new self($current, $opponent);
        }
        self::$_instances[$hash]->addCount($instanceCount);

        return self::$_instances[$hash];
    }

    private function __construct(Player $current, Player $opponent) {
        $this->_current = $current;
        $this->_opponent = $opponent;
    }

    public function reset() {
        $this->_current->reset();
        $this->_opponent->reset();
        self::$_instances = [];
    }

    public function getRolls() {
        return $this->_rolls;
    }

    private function addCount($instanceCount) {
        $this->_instanceCount += $instanceCount;
    }

    public function regularThrow($winScore) {
        $throwScore = 0;
        for ($i = 1; $i <= 3; $i++) {
            $this->_rolls++;
            $throwScore += $this->_nextDie++;
            $this->_nextDie = ($this->_nextDie - 1 % 100) + 1;
        }

        $this->_current->increasePos($throwScore);
        if ($this->_current->getScore() >= $winScore) {
            $this->_current->setWinner($this->_instanceCount);
            return false;
        }
        $this->flipTurn();
        return true;
    }

    private function flipTurn() {
        $tmp = $this->_current;
        $this->_current = $this->_opponent;
        $this->_opponent = $tmp;
    }

    private function quantumThrow($winScore) {
        for ($die1 = 1; $die1 <= 3; $die1++) {
            for ($die2 = 1; $die2 <= 3; $die2++) {
                for ($die3 = 1; $die3 <= 3; $die3++) {
                    $current = clone $this->_current;
                    $current->increasePos($die1 + $die2 + $die3);
                    $opponent = clone $this->_opponent;

                    if ($current->getScore() >= $winScore) {
                        $current->setWinner($this->_instanceCount);
                    } else {
                        self::create($opponent, $current, $this->_instanceCount);
                    }
                }
            }
        }
    }

    public static function quantumThrowAll($winScore) {
        foreach (self::$_instances as $hash => $universe) {
            $universe->quantumThrow($winScore);
            unset(self::$_instances[$hash]);
        }

        return count(self::$_instances) > 0;
    }
}
