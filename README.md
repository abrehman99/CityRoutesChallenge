# CityRoutesChallenge
The goal of this simple app is to calculate 
shortest paths between cities provided a set 
of cities and their connections. The app is intended to be easy to understand and work with. It also should allow easy extension of the functionalities.

**How to use**

Make a GET petition to the root of the application.
Provide following parameters:

    "startCity": Required. The city from which you wish to calculate the route/s.
    "destinationCity": Optional. The city you wish to reach from startCity.
    
    
_If only "startCity" is provided the application will compute and
 respond with the routes to every city starting from "startCity"._

_If both parameters are provided the application will compute and return the shortest path between the two cities including the full route._



**Data constraints**

- The application allows the following cities:
    
      $cities=['Logroño','Zaragoza','Teruel','Madrid','Lleida','Alicante','Castellón','Segovia','Ciudad Real'];
      
- Following are the (ordered) connections between the cities. The order followed is the same 
    as the array. e.g 'Teruel' is at the third place in $cities, so its connections are at the third place in $connections. 
    
      
      $connections=[
                  [0,4,6,8,0,0,0,0,0],
                  [4,0,2,0,2,0,0,0,0],
                  [6,2,0,3,5,7,0,0,0],
                  [8,0,3,0,0,0,0,0,0],
                  [0,2,5,0,0,0,4,8,0],
                  [0,0,7,0,0,0,3,0,7],
                  [0,0,0,0,4,3,0,0,6],
                  [0,0,0,0,8,0,0,0,4],
                  [0,0,0,0,0,7,6,4,0]
              ];
     
      
    