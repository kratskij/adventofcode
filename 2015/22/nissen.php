<?php

$spells = [
	new Spell(
		"MagicMissile",
		53,
		false,
		function($boss, $wizard) {
			$boss->hit(4);
		}
	),
	new Spell(
		"Drain",
		73,
		false,
		function($boss, $wizard) {
			$boss->hit(2);
			$wizard->heal(2);
		}
	),
	new Spell(
		"Shield",
		113,
		6,
		function($boss, $wizard) {
			$wizard->armor(7);
		},
		function($boss, $wizard) {
			$wizard->armor(0);
		},
		true
	),
	new Spell(
		"Poison",
		173,
		6,
		function($boss, $wizard) {
			$boss->hit(3);
		}
	),
	new Spell(
		"Recharge",
		229,
		5,
		function($boss, $wizard) {
			$wizard->mana(101);
		}
	)
];

$wizard = new Wizard(50, 500, 0);
$boss = new Boss(55, 8);
echo "Part 1: " . play($wizard, $boss, $spells, false, true) . "\n";

$wizard = new Wizard(50, 500, 0);
$boss = new Boss(55, 8);
echo "Part 2: " . play($wizard, $boss, $spells, true, true) . "\n";

function logger($msg) {
	if (false) {
		echo $msg."\n";
	}
}
function play($w, $b, $spells, $hardmode, $init = false) {
	static $minCost;
	if ($init) {
		$minCost = PHP_INT_MAX;
	}
	if ($w->getCost() >= $minCost) {
		return $minCost;
	}
	logger("\n-- Wizard's turn --\n");
	if ($hardmode) {
		$w->hit(1);
		if (!$w->alive()) {
			return $minCost;
		}
	}
	$w->dump();
	$b->dump();
	$w->attack($b);
	if (!$b->alive()) {
		logger("Boss is dead!\n");
		if ($w->getCost() < $minCost) {
			$minCost = $w->getCost();
		}
		return $minCost;
	}

	$affordableSpells = array_filter($spells, function($spell) use ($w) {
		return ($w->getMana() >= $spell->price());
	});
	if (count($affordableSpells) == 0) {
		logger("Can't afford any spells. Wizard is dead.");
		return $minCost;
	}

	foreach ($affordableSpells as $spell) {
		logger("SPELL: " . $spell->getName()."\n");
		$wizard = clone $w;
		$boss = clone $b;

		if (!$wizard->buySpell($spell, $boss)) {
			continue;
		}
		if ($wizard->getCost() >= $minCost) {
			continue;
		}
		logger("\n-- Boss turn --\n");
		
		$wizard->dump();
		$boss->dump();	
		$wizard->attack($boss);
		if (!$boss->alive()) {
			logger("Boss is dead!\n");
			if ($wizard->getCost() < $minCost) {
				$minCost = $wizard->getCost();
			}
			continue;
		}
		$boss->attack($wizard);
		if (!$wizard->alive()) {
			logger("Wizard is dead!\n");
			continue;
		}
		$minCost = play($wizard, $boss, $spells, $hardmode);
		#echo "mincost: $minCost, " . $wizard->getCost() . "\n";
	}

	return $minCost;
}



abstract class Person
{
	protected $hitpoints;

	public function hit($value)
	{
		$this->hitpoints -= max(1, $value);
	}

	public function alive()
	{
		return ($this->hitpoints > 0);
	}
}

class Boss extends Person
{
	private $damage;

	public function __construct($hitpoints, $damage)
	{
		$this->hitpoints = $hitpoints;
		$this->damage = $damage;
	}

	public function attack($wizard) {
		logger("Boss attacks for " . $this->damage . " damage.");
		$wizard->hit($this->damage);
	}

	public function dump()
	{
		logger("- Boss has " . $this->hitpoints . " hit points");
	}
}

class Wizard extends Person
{
	private $mana;
	private $armor;
	private $spells = [];
	private $cost = 0;

	public function __construct($hitpoints, $mana, $armor)
	{
		$this->hitpoints = $hitpoints;
		$this->mana = $mana;
		$this->armor = $armor;
	}

	public function hit($value)
	{
		$this->hitpoints -= max(1, $value - $this->armor);
	}

	public function attack($boss)
	{
		foreach ($this->spells as $key => &$container) {
			$container["count"]--;
			$container["spell"]->cast($boss, $this);

			logger("Wizard casts $key. Timer is now " . $container["count"] . "");
			if ($container["count"] == 0) {
				$container["spell"]->destruct($boss, $this);
				unset($this->spells[$key]);
			}
		}
	}
	public function getArmor()
	{
		return $this->armor;
	}

	public function getMana()
	{
		return $this->mana;
	}
	public function heal($points) {
		$this->hitpoints += $points;
	}

	public function buySpell($spell, $boss)
	{
		if ($this->mana > $spell->price() && !isset($this->spells[$spell->getName()])) {
			$this->mana -= $spell->price();
			$this->cost += $spell->price();
			if ($spell->isInstant()) {
				$spell->cast($boss, $this);
			}
			if ($spell->getCount()) { 
				$this->spells[$spell->getName()] = [
					"count" => $spell->getCount(),
					"spell" => $spell
				];
			}
			logger("Wizard buys " . $spell->getName() . ".");
			return true;
		}
		return false;
	}

	public function getCost()
	{
		return $this->cost;
	}

	public function mana($value)
	{
		$this->mana += $value;
	}

	public function armor($value)
	{
		$this->armor = $value;
	}

	public function dump()
	{
		logger("- Wizard has " . $this->hitpoints . " hit points, " .
		$this->armor . " armor, " . $this->mana . " mana");
	}
	
}

class Spell
{
	private $name;
	private $mana;
	private $count;
	private $action;
	private $instant;

	public function __construct($name, $mana, $count, $action, $destruct = false, $instant = false)
	{
		$this->name = $name;
		$this->mana = $mana;
		$this->count = $count;
		$this->action = $action;
		$this->destruct = ($destruct == false) ? function($boss, $wizard) { return; } : $destruct;
		$this->instant = $instant || $count === false;
	}

	public function isInstant() {
		return $this->instant;
	}

	public function getCount(){
		return $this->count;
	}

	public function getName()
	{
		return $this->name;
	}

	public function cast($boss, $wizard)
	{
		call_user_func($this->action, $boss, $wizard);
	}

	public function price()
	{
		return $this->mana;
	}

	public function destruct($boss, $wizard)
	{
		call_user_func($this->destruct, $boss, $wizard);
	}

}