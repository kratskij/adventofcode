<?php

$input = explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "nissen.txt"));
$spoons = 100;
# part 2:
# $maxCalories =  500;

$ingredients = [];
foreach ($input as $row) {
  list($name, $propstr) = explode(": ", $row);
  preg_match_all("/([a-z]+) ([\-\d]+)/", $propstr, $matches);
  $ingredients[$name] = [];
  foreach ($matches[1] as $k => $prop) {
    $ingredients[$name][$prop] = $matches[2][$k];
  }
}

$values = combine(
  $ingredients,
  $spoons,
  isset($maxCalories) ? $maxCalories : false
);

echo "Best taste" .
  (isset($maxCalories) ? " with " . $maxCalories . " calories" : "") .
  ": " . max($values) . "\n";

function combine(
  $ingredients,
  $spoons,
  $maxCal = false,
  $properties = null,
  $availableIng = null,
  $calories = 0
) {
  if ($availableIng === null) { $availableIng = array_keys($ingredients); }
  $ingredient = $ingredients[array_shift($availableIng)];
  if ($properties === null) {
    $properties = array_map(function($a) { return 0; }, $ingredient);
    unset($properties['calories']);
  }
  $values = [];

  if (empty($availableIng)) {
    $value = 1;
    foreach ($properties as $prop => $v) {
      $v += $spoons * $ingredient[$prop];
      $value *= $v;
      if ($value <= 0) {
        return [];
      }
    }
    $calories += $spoons * $ingredient['calories'];
    array_push($values, (!$maxCal || $calories == $maxCal) ? $value : 0);
    return $values;
  }

  for ($i = 0; $i <= $spoons; $i++) {
    $props = $properties;
    foreach ($props as $prop => $value) {
      $props[$prop] += $i * $ingredient[$prop];
    }
    $cals = $calories + $i * $ingredient['calories'];
    $values = array_merge(
      $values,
      combine($ingredients, $spoons-$i, $maxCal, $props, $availableIng, $cals)
    );
  }
  return $values;
}
