<?php

ini_set('memory_limit','2048M');

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim(true);
$input = $ir->lines();

$boost = 0;
while (true) {
    list($immunes, $infections) = getGroupCollections($input, $boost);
    while ($battleResult = fight($infections, $immunes));
    if ($boost == 1) {
        echo "P1: " . ($immunes->countArmy() + $infections->countArmy()) . "\n";
    }

    if ($battleResult === false && $immunes->countArmy() > 0) {
        echo "P2: {$immunes->countArmy()}\n";
        exit;
    }
    $boost++;
}

function getGroupCollections($input, $boost = 0) {
    $pattern = "/^(?<units>\d+)\sunits\s.*?(?<hitPoints>\d+)\shit\spoints\s\(?((?<weakorimmune>immune|weak)\sto\s(?<weakorimmuneto>[\w+\,\s]+))?\;?\s?((?<weakorimmune2>immune|weak)\sto\s(?<weakorimmuneto2>[\w+\,\s]+))?.*?(?<damage>\d+)\s(?<damageType>\w+)\sdamage\s.*initiative\s(?<initiative>\d+)$/i";
    $immuneSystemGroups = $infectionGroups = [];
    foreach ($input as $line) {
        if ($line == "Immune System:") {
            $id = 0;
            $idPrefix = "Immune System group ";
            $ref = &$immuneSystemGroups;
            continue;
        } else if ($line == "Infection:") {
            $id = 0;
            $idPrefix = "Infection group ";
            $ref = &$infectionGroups;
            continue;
        } else if ($line == "") {
            continue;
        }
        preg_match($pattern, $line, $matches);
        $group = new Group(
            $idPrefix . ++$id,
            (int)$matches["units"],
            (int)$matches["hitPoints"],
            (int)$matches["damage"] + (($idPrefix == "Immune System group ") ? $boost : 0),
            $matches["damageType"],
            (int)$matches["initiative"]
        );

        if ($matches["weakorimmune"] == "weak") {
            $group->setWeaknesses(explode(", ", $matches["weakorimmuneto"]));
        } elseif ($matches["weakorimmune"] == "immune") {
            $group->setImmunities(explode(", ", $matches["weakorimmuneto"]));
        }
        if ($matches["weakorimmune2"] == "weak") {
            $group->setWeaknesses(explode(", ", $matches["weakorimmuneto2"]));
        } elseif ($matches["weakorimmune2"] == "immune"){
            $group->setImmunities(explode(", ", $matches["weakorimmuneto2"]));
        }
        $ref[] = $group;
    }

    $immunes = new GroupCollection($immuneSystemGroups, $infectionGroups);
    $infections = new GroupCollection($infectionGroups, $immuneSystemGroups);

    return [$immunes, $infections];
}

function fight($infections, $immunes) {
    $queue = GroupCollection::getAttackQueue($infections, $immunes);

    if (!$queue) {
        return false;
    }

    $sumKIA = 0;
    while ($current = array_shift($queue)) {
        $KIA = $current["attacker"]->attack($current["target"]);
        $sumKIA += $KIA;
    }
    if ($sumKIA == 0) {
        return null; // stale mode
    }
    return true;
}

class Group {
    private $_id;
    private $_units;
    private $_hitPoints;
    private $_damage;
    private $_damageType;
    private $_initiative;

    private $_reductions = [
        "radiation" => 1,
        "fire" => 1,
        "slashing" => 1,
        "cold" => 1,
        "bludgeoning" => 1,
    ];

    function __construct(
        $id,
        $units,
        $hitPoints,
        $damage,
        $damageType,
        $initiative
    ) {
        $this->_id = $id;
        $this->_units = $units;
        $this->_hitPoints = $hitPoints;
        $this->_damage = $damage;
        $this->_damageType = $damageType;
        $this->_initiative = $initiative;
    }

    public function setWeaknesses(array $weaknesses) {
        foreach ($weaknesses as $w) {
            $this->_reductions[$w] *= 2;
        }
    }

    public function setImmunities(array $immunities) {
        foreach ($immunities as $i) {
            $this->_reductions[$i] = 0;
        }
    }

    public function getEffectivePower() {
        return $this->_units * $this->_damage;
    }

    public function getInitiative() {
        return $this->_initiative;
    }

    public function getId() {
        return $this->_id;
    }

    public function selectTarget(array $enemies) {
        #foreach ($enemies as $e) {
        #    echo $this->getId() . " would deal " . $e->getId() . " " . $e->damageDealt($this->_damageType, $this->getEffectivePower()) . " damage\n";
        #}
        usort($enemies, function($a, $b) {
            $aDD = $a->damageDealt($this->_damageType, $this->getEffectivePower());
            $bDD = $b->damageDealt($this->_damageType, $this->getEffectivePower());

            if ($aDD == $bDD) {
                $aEP = $a->getEffectivePower();
                $bEP = $b->getEffectivePower();
                if ($aEP == $bEP) {
                    return $a->getInitiative() < $b->getInitiative() ? 1 : -1;
                }
                return $aEP < $bEP ? 1 : -1;
            }
            return $aDD < $bDD ? 1 : -1;
        });
        $target = array_shift($enemies);
        if ($target && !$target->damageDealt($this->_damageType, $this->getEffectivePower())) {
            return false;
        }
        return $target;
    }

    private function damageDealt($damageType, $effectivePower) {
        return $this->_reductions[$damageType] * $effectivePower;
    }

    public function attack($enemy) {
        return $enemy->damage($this->_damageType, $this->getEffectivePower());
    }

    public function damage($damageType, $effectivePower) {
        $damageDealt = $this->damageDealt($damageType, $effectivePower);
        $unitsKilled = min(floor($damageDealt / $this->_hitPoints), $this->_units);
        $this->_units -= $unitsKilled;

        return $unitsKilled;
    }

    public function alive() {
        return $this->_units > 0;
    }

    public function getUnits() {
        return $this->_units;
    }

    public function __toString() {
        return "{$this->getId()} contains {$this->_units} units";
    }
}

class GroupCollection {
    private $_groups;
    private $_enemies;

    public function __construct(array $groups, array $enemies) {
        $this->_groups = $groups;
        $this->_enemies = $enemies;
    }

    private function findTargets() {
        usort($this->_groups, function($a, $b) {
            $aEP = $a->getEffectivePower();
            $bEP = $b->getEffectivePower();
            if ($aEP == $bEP) {
                return $a->getInitiative() < $b->getInitiative() ? 1 : -1;
            }
            return $aEP < $bEP ? 1 : -1;
        });

        $participants = []; // who attacks who?
        $remaining = array_filter( // who can we still select to attack?
            $this->_enemies,
            function($e) {
                return $e->alive();
            }
        );
        foreach ($this->_groups as $attacker) {
            if ($attacker->alive() && $target = $attacker->selectTarget($remaining)) {
                $participants[] = ["attacker" => $attacker, "target" => $target];
                foreach ($remaining as $k => $r) {
                    if ($r->getId() == $target->getId()) {
                        unset($remaining[$k]);
                        break;
                    }
                }
            }
        }

        return $participants;
    }

    public static function getAttackQueue(GroupCollection $a, GroupCollection $b) {
        $queue = array_merge($a->findTargets(), $b->findTargets());
        usort($queue, function($qA, $qB) {
            return ($qA["attacker"]->getInitiative() < $qB["attacker"]->getInitiative()) ? 1 : -1;
        });

        return $queue;
    }

    public function __toString() {
        $ret = "";
        foreach ($this->_groups as $g) {
            if ($g->alive()) {
                $ret .= "$g\n";
            }
        }
        return $ret;
    }

    public function countArmy() {
        $sum = 0;
        foreach ($this->_groups as $g) {
            if ($g->alive()) {
                $sum += $g->getUnits();
            }
        }
        return $sum;
    }
}
