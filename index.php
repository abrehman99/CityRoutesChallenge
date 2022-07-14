<?php

require_once(realpath(dirname(__FILE__) . '/./src/controllers/CityRoutesController.php'));


if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    echo 'Method not allowed';
    http_response_code('400');
    return;
}


$cityRoutesController = new CityRoutesController();


if (isset($_GET['destinationCity'])) {
    $cityRoutesController->shortestPathBetweenTwoCities($_GET['startCity'], $_GET['destinationCity']);
    return;
}

$cityRoutesController->shortestPathsFromCity($_GET['startCity']);
return;









