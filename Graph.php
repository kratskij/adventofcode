<?php

/*
 * This class is heavily inspired by the following graph example:
 * https://www.sitepoint.com/data-structures-4/
 *
 * See 2018 day 20 for example usage
 */

class Graph
{
    private $_graph;
    private $_origin;
    private $_distances = [];

    public function __construct($graph, $origin) {
        $this->_graph = $graph;
        $this->_origin = $origin;

        $this->solveAllDistances();
    }

    private function solveAllDistances() {
        $queue = new SplQueue();

        $queue->enqueue($this->_origin);

        $path = array();
        $path[$this->_origin] = new SplDoublyLinkedList();
        $path[$this->_origin]->setIteratorMode(
            SplDoublyLinkedList::IT_MODE_FIFO|SplDoublyLinkedList::IT_MODE_KEEP
        );

        $path[$this->_origin]->push($this->_origin);

        $found = false;

        while (!$queue->isEmpty()) {
            $t = $queue->dequeue();
            if (!empty($this->_graph[$t])) {
                // for each adjacent neighbor
                foreach ($this->_graph[$t] as $coords) {
                    if (!isset($this->_distances[$coords])) {
                        $queue->enqueue($coords);
                        // add coords to current path
                        $path[$coords] = clone $path[$t];
                        $path[$coords]->push($coords);

                        // self will be included, which it should not be, so we subtract 1
                        $this->_distances[$coords] = count($path[$coords]) - 1;
                    }
                }
            }
        }
    }

    public function distance($coords) {
        return $this->_distances[$coords];
    }

    public function maxShortestPath() {
        return max($this->_distances);
    }

    public function filterDistances($callback) {
        return array_filter($this->_distances, $callback);
    }
}
