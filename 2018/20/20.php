<?php

ini_set('memory_limit','2048M');

define("ROOM", ".");
define("WALL", "#");
define("UNKNOWN", "?");
define("EW_DOOR", "|");
define("NS_DOOR", "-");
define("BRANCH", "Y");

$test = isset($argv[1]) && $argv[1] == "test";
$file = ($test) ? "test" : "input";

require_once(__DIR__."/../inputReader.php");

$ir = (new InputReader(__DIR__ . DIRECTORY_SEPARATOR . $file))->trim();
$input = $ir->chars();
array_shift($input);
array_pop($input);

$struct = [];
$y = 0;
$x = 0;
$stack = [[$y, $x]];
foreach ($input as $char) {
    $currIdx = $y . "_" . $x;
    switch ($char) {
        case "E":
            $x++;
            $struct[$currIdx][] = $y . "_" . $x;
            break;
        case "S":
            $y++;
            $struct[$currIdx][] = $y . "_" . $x;
            break;
        case "W":
            $x--;
            $struct[$currIdx][] = $y . "_" . $x;
            break;
        case "N":
            $y--;
            $struct[$currIdx][] = $y . "_" . $x;
            break;
        case "(":
            array_push($stack, [$y, $x]);
            break;
        case ")":
            list($y, $x) = array_pop($stack);
            break;
        case "|":
            list($y, $x) = end($stack);
            break;
        default:
            throw new Exception("WAT");
    }
}

$g = new Graph($struct);
$max = -INF;
$count = count($struct);
foreach (array_keys($struct) as $k => $s) {
    echo "\rChecking $k of $count";
    $max = max($max, $g->breadthFirstSearch('0_0', $s));
}
echo "\nPart 1: $max\n";

class Graph
{
  protected $graph;
  protected $visited = array();

  public function __construct($graph) {
    $this->graph = $graph;
  }

  // find least number of hops (edges) between 2 nodes
  // (vertices)
  public function breadthFirstSearch($origin, $destination) {
    // mark all nodes as unvisited
    foreach ($this->graph as $vertex => $adj) {
      $this->visited[$vertex] = false;
    }

    // create an empty queue
    $q = new SplQueue();

    // enqueue the origin vertex and mark as visited
    $q->enqueue($origin);
    $this->visited[$origin] = true;

    // this is used to track the path back from each node
    $path = array();
    $path[$origin] = new SplDoublyLinkedList();
    $path[$origin]->setIteratorMode(
      SplDoublyLinkedList::IT_MODE_FIFO|SplDoublyLinkedList::IT_MODE_KEEP
    );

    $path[$origin]->push($origin);

    $found = false;
    // while queue is not empty and destination not found
    while (!$q->isEmpty() && $q->bottom() != $destination) {
      $t = $q->dequeue();

      if (!empty($this->graph[$t])) {
        // for each adjacent neighbor
        foreach ($this->graph[$t] as $vertex) {
          if (!isset($this->visited[$vertex]) || !$this->visited[$vertex]) {
            // if not yet visited, enqueue vertex and mark
            // as visited
            $q->enqueue($vertex);
            $this->visited[$vertex] = true;
            // add vertex to current path
            $path[$vertex] = clone $path[$t];
            $path[$vertex]->push($vertex);
          }
        }
      }
    }

    if (isset($path[$destination])) {
      return count($path[$destination]);
      /*$sep = '';
      foreach ($path[$destination] as $vertex) {
        echo $sep, $vertex;
        $sep = '->';
      }
      echo "n";*/
    }
    else {
      echo "No route from $origin to $destinationn";
    }
  }
}
