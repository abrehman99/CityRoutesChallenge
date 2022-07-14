<?php
require_once(realpath(dirname(__FILE__) . './IShortestRouteCalc.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/KeyValueResponse.php'));


/**
 * Computes shortest paths using Dijkstra Algorithm approach
 * Class DijkstraShortestRoute
 */
class DijkstraShortestRoute implements IShortestRouteCalc
{

    /**
     *
     * Computes the distance and the route of the shortest path from $startNode to $destinationNode
     *
     * @param string $startNode
     * @param string $destinationNode
     * @param array $nodes
     * @param array $connections can only contain values >= 0
     *
     *
     *
     * @return KeyValueResponse|null response contains the following keys:
     *
     *                                  "From"     => startNode
     *                                  "To"       => destinationNode
     *                                  "distance" => minimum distance from startNode to destinationNode. 0 if no route.
     *                                  "route"    => lists all nodes part of the shortest path, separated by "=>"
     *
     */
    public function shortestPathBetweenTwoNodes(string $startNode, string $destinationNode, array $nodes, array $connections): ?KeyValueResponse
    {
        //valdate input
        if ($startNode == $destinationNode || array_search($startNode, $nodes) === false || array_search($destinationNode, $nodes) === false) return null;

        //Get dijkstra table
        $distanceTable = $this->getDijkstraDistanceTableFromNode($startNode, $nodes, $connections);



        //Find destinationNode in this table
        $destinationNodeIndex = array_search($destinationNode, $nodes);
        $resRow = $distanceTable[$destinationNodeIndex];

        //Get info from this table

        //Get distance
        $totalDistance = $resRow['distance'];

        //Build route from destinationNode to startNode using the prevNode column of this table
        $route = [$destinationNode, $resRow['prevNode']];

        $prevNode = $resRow['prevNode'];
        $i = 0;
        while ($prevNode != $startNode && !empty($prevNode) && $i < sizeof($nodes)) {
            $itIndex = array_search($prevNode, $nodes);
            $itRow = $distanceTable[$itIndex];
            $prevNode = $itRow['prevNode'];
            $route[] = $prevNode;
        }

        //Format result

        $route = array_reverse($route);

        $res = new KeyValueResponse();


        if ($totalDistance > 0) {
            $res->addData('From', $startNode);
            $res->addData('To', $destinationNode);
            $res->addData('distance', $totalDistance);
            $res->addData('route', implode(' => ', $route));
        }


        return !empty($res) ? $res : null;
    }



    /**
     * Computes the shortest distance from $node to every other node in $nodes using Dijkstra algorithm.
     * @param string $node starting node
     * @param array  $nodes
     * @param array  $connections
     * @return KeyValueResponse|null    keys are the destination nodes
     *                                  values are the minimum distance to reach them from $node
     *
     */
    public function shortestPathsFromNode(string $node, array $nodes, array $connections): ?KeyValueResponse
    {
        if (array_search($node, $nodes) === false) return null;

        $distanceTable = $this->getDijkstraDistanceTableFromNode($node, $nodes, $connections);


        $res = new KeyValueResponse();
        foreach ($distanceTable as $tableRow) {
            if ($tableRow['node'] != $node) {
                $res->addData($tableRow['node'], $tableRow['distance']);
            }
        }

        return $res;
    }







    /**
     * Applies the Dijkstra Algorithm to get the minimum distances from the start node to all other nodes
     *      It also returns the previous node used to reach each destination for the minimum distance.
     *      This allows us to get the complete route by iterating backwards from destination
     *
     *
     * ALGORITHM STEPS
     *   1. INITIALIZE TABLE WITH DISTANCES FROM START NODE TO ALL NODES, OR ZERO IF THERE IS NO DIRECT ROUTE
     *       1.1 INTIALIZE UNVISITED NOTES.
     *       1.2 SO FAR ALL NODES APART FROM THE START NODE ARE CONSIDERED UNVISITED.
     *   2. ITERATE OVER ALL UNVISITED NODES
     *       2.1 EACH ITERATION CHOOSE THE NODE WHICH HAS THE LESSER DISTANCE FROM THE START NODE (DIRECTLY OR INDRECTLY)
     *           2.1.1 FOR THE CHOSEN NODE GET ITS NEIGHBOURS
     *           2.1.2 CHECK IF DISTANCE FROM THE START NODE TO THE NEIGHBOURS VIA THE CHOSEN NODE IS SHORTER THAN THE
     *                 PREVIOUS TABLE DISTANCE AND UPDATE ACCORDINGLY
     *           2.1.3 CHOSEN NODE IS NO LONGE UNVISITED
     *
     *
     * @param $start
     * @param $nodes
     * @param $connections
     * @return array returns a dijkstra table style array with format:
     *                      'node'     => $node, //destination node
     *                      'distance' => 0,     //distance from $start
     *                      'prevNode' => ''     //previous node used to reach each destination
     */
    private function getDijkstraDistanceTableFromNode($start, $nodes, $connections)
    {
        //1. INITIALIZE TABLE WITH DISTANCES FROM START NODE TO ALL NODES, OR ZERO IF THERE IS NO DIRECT ROUTE
        $distanceTable = array();
        $startIndex = array_search($start, $nodes);

        //1.1 INTIALIZE UNVISITED NOTES.
        $unvisited = array();

        foreach ($nodes as $index => $node) {
            if ($node == $start) {
                $distanceTable[] = array(
                    'node' => $node,
                    'distance' => 0, //zero means no route
                    'prevNode' => ''
                );
            } else {
                //1.2 SO FAR ALL NODES APART FROM THE START NODE ARE CONSIDERED UNVISITED.
                $unvisited[] = $node;
                //Populate with distance from start node
                $distanceTable[] = array(
                    'node' => $node,
                    'distance' => $connections[$startIndex][$index],
                    'prevNode' => $connections[$startIndex][$index] > 0 ? $start : ''
                );
            }
        }
        unset($node);

        //2. ITERATE OVER ALL UNVISITED NODES
        $i = 0; //precautonary
        while (!empty($unvisited) && $i < sizeof($nodes)) {
            $minDist = null;
            $curNode = "";

            //2.1 EACH ITERATION CHOOSE THE NODE WHICH HAS THE LESSER DISTANCE FROM THE START NODE (DIRECTLY OR INDRECTLY)

            foreach ($distanceTable as $nodeRow) {
                $notVisited = in_array($nodeRow['node'], $unvisited);
                $isClosestNeb = $nodeRow['distance'] > 0 && (empty($minDist) || $nodeRow['distance'] < $minDist);
                if ($notVisited && $isClosestNeb) {
                    $curNode = $nodeRow['node'];
                    $minDist = $nodeRow['distance'];
                }
            }
            if (empty($curNode)) continue;

            //  2.1.1 FOR THE CHOSEN NODE GET ITS NEIGHBOURS (DIRECT ROUTE FROM CHOSEN NODE)
            $distFromStartToCurNode = !empty($minDist) ? $minDist : 0;
            $curNodeNebs = array();
            $curNodeIndex = array_search($curNode, $nodes);

            foreach ($nodes as $index => $node) {
                if ($node != $curNodeIndex && $connections[$curNodeIndex][$index] > 0) {
                    $curNodeNebs[] = array(
                        'node' => $node,
                        'distance' => $connections[$curNodeIndex][$index] > 0 ? $connections[$curNodeIndex][$index] + $distFromStartToCurNode : 0,
                        'prevNode' => $connections[$curNodeIndex][$index] > 0 ? $curNode : ''
                    );
                }
            }


            //2.1.2 CHECK IF DISTANCE FROM THE START NODE TO THE NEIGHBOURS VIA THE CHOSEN NODE IS SHORTER THAN THE
            //      PREVIOUS TABLE DISTANCE AND UPDATE ACCORDINGLY

            foreach ($curNodeNebs as $neb) {
                $nebIndex = array_search($neb['node'], $nodes);
                $oldDist = $distanceTable[$nebIndex]['distance'];
                if ($oldDist == 0 || $neb['distance'] > 0 && $neb['distance'] < $oldDist) {
                    $distanceTable[$nebIndex]['distance'] = $neb['distance'];
                    $distanceTable[$nebIndex]['prevNode'] = $curNode;
                }
            }


            //2.1.3 CHOSEN NODE IS NO LONGE UNVISITED
            if (($key = array_search($curNode, $unvisited)) !== false) {
                unset($unvisited[$key]);
            }

            $i++;

        }//END OF ITERATION. RETURN UPDATED DIJKSTRA TABLE

        //reset values for start node
        $distanceTable[$startIndex]['distance'] = 0;
        $distanceTable[$startIndex]['prevNode'] = '';


        return $distanceTable;
    }

}