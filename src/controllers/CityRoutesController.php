<?php

require_once(realpath(dirname(__FILE__) . '/../models/CityRoutesModel.php'));
require_once(realpath(dirname(__FILE__) . '/../libraries/DijkstraShortestRoute.php'));

/**
 * Class CityRoutesController
 */
class CityRoutesController
{

    /**
     * @var string[]
     */
    private $cities;
    /**
     * @var int[][]
     */
    private $connections;

    /**
     * Initialize data necessary to perform functions
     * CityRoutesController constructor.
     */
    public function __construct()
    {
        $model = new CityRoutesModel();
        $this->cities = $model->getCities();
        $this->connections = $model->getConnections();
    }

    /**
     * Returns the minimum distance between the start and the destination city.
     * @param $startCity
     * @param $destinationCity
     *
     * echos JSON and returns HTTP response
     */
    public function shortestPathBetweenTwoCities($startCity, $destinationCity){
        header('Content-Type: application/json; charset=utf-8');
        $res = new KeyValueResponse();

        //validations
        if(empty(trim($startCity)) || empty(trim($destinationCity))){
            $res->addData('status', 'error');
            $res->addData('errorMsg', 'startCity and destinationCity are both required');
            echo $res->getJSONResponse();
            http_response_code('400');
            return;
        }
        if (in_array($startCity, $this->cities) === FALSE || in_array($destinationCity, $this->cities) === FALSE){
            $res->addData('status', 'error');
            $errorMsg = 'startCity and destinationCity should be one of the following: '. implode("-", $this->cities);
            $res->addData('errorMsg', $errorMsg);

            echo $res->getJSONResponse();
            http_response_code('400');
            return;
        }
        //end validations


        $dijkstraShortestRoute = new DijkstraShortestRoute();
        $routeResponse = $dijkstraShortestRoute->shortestPathBetweenTwoNodes($startCity, $destinationCity, $this->cities, $this->connections);

        if($routeResponse == null){
            $res->addData('status', 'error');
            $res->addData('errorMsg', 'Could not calculate any valid route');
            echo $res->getJSONResponse();
            http_response_code('500');
            return;
        }

        echo $routeResponse->getJSONResponse();
    }

    /**
     * Returns the minimum distance from startCity to all other cities
     * @param $startCity
     *
     * echos JSON and returns HTTP response
     */
    public function shortestPathsFromCity($startCity){
        header('Content-Type: application/json; charset=utf-8');
        $res = new KeyValueResponse();

        //Validations
        if(empty(trim($startCity))){
            $res->addData('status', 'error');
            $res->addData('errorMsg', 'startCity is required');
            echo $res->getJSONResponse();
            http_response_code('400');
            return;
        }

        if (in_array($startCity, $this->cities) === FALSE){
            $res->addData('status', 'error');
            $errorMsg = 'startCity should be one of the following: '. implode("-", $this->cities);
            $res->addData('errorMsg', $errorMsg);
            echo $res->getJSONResponse();
            http_response_code('400');
            return;
        }
        //end validations


        $dijkstraShortestRoute = new DijkstraShortestRoute();
        $routeResponse = $dijkstraShortestRoute->shortestPathsFromNode($startCity, $this->cities, $this->connections);

        if($routeResponse == null){
            $res->addData('status', 'error');
            $res->addData('errorMsg', 'Could not calculate any valid route');
            echo $res->getJSONResponse();
            http_response_code('500');
            return;
        }

        echo $routeResponse->getJSONResponse();
    }
}