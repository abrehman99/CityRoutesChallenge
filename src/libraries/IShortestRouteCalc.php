<?php

require_once(realpath(dirname(__FILE__) . '/../helpers/KeyValueResponse.php'));

/**
 * Provides methods to calculate shortest path
 * Interface IShortestRouteCalc
 */
interface IShortestRouteCalc
{

    /**
     * Computes the distance and the route of the shortest path from $startNode to $destinationNode
     * @param string $startNode
     * @param string $destinationNode
     * @param array  $nodes contains all the nodes to consider
     *
     * @param array  $connections contains the distances of every direct route between nodes (0 if no direct route )
     *                               Should be sorted in the same order as $nodes, e.g if node 'C' is in the third
     *                               position of $nodes, its connections should also be in the third position
     *                               of the $connections array
     *
     *
     *
     * @return KeyValueResponse|null Response includes distance and the route followed to achieve that distance
     */
    public function shortestPathBetweenTwoNodes(string $startNode, string $destinationNode, array $nodes, array $connections) : ?KeyValueResponse;

    /**
     * Computes the distance of the shortest path from $startNode to every other node in $nodes
     * @param string $startNode the node from which all paths and distances will be calculated
     * @param array  $nodes
     * @param array  $connections
     *
     *
     *
     * @return KeyValueResponse|null Response includes distance to every other node in nodes different from startNode
     */
    public function shortestPathsFromNode(string $startNode, array $nodes, array $connections): ?KeyValueResponse;
}