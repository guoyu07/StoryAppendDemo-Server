controllers.ViewMapCtrl = function($scope, $rootScope, $http, $window) {
    $scope.viewpoints = [];
    $scope.init = function() {
        var longitude, latitude, location, query;
        location = window.location.href;
        query = location.split('?')[1].split('&');
        latitude = query[1].split('=')[1];
        longitude = query[0].split('=')[1];
        $scope.viewpoints.push(['', latitude, longitude, 1]);
        $rootScope.$emit('loadStatus', false);
    };

    $scope.init();

    $window.onload = function() {
        var map, current_markers;
        map = HImap.init('map-canvas');
        current_markers = HImap.drawMarkers(map, $scope.viewpoints);
        //current_flightPath = HImap.drawFlightPath( map, viewpoints );
        HImap.suitBounds(map, $scope.viewpoints);
    };
}

app.controller('ViewMapCtrl', ['$scope', '$rootScope', '$http', '$window', controllers.ViewMapCtrl]);

//var viewpoints = [
//  ['Tour Eiffel', 48.858380, 2.294462, 1],
//  ['Notre Dame Cathedral', 48.852834, 2.350267, 2]
//];

//var dayTourCoords = [
//  [
//    ['Place Charles de Gaulle', 48.873710, 2.295037, 1],
//    ['Louvre Museum', 48.860617, 2.337645, 2],
//    ['The Westin Paris - Vendome', 48.865745, 2.327442, 3]
//  ],
//  [
//    ['Place des Vosges', 48.855617, 2.365528, 1],
//    ['Saint-Paul', 48.854473, 2.361453, 2]
//  ]
//];

//var map;
//var current_markers, current_flightPath;
//window.onload = function() {
//  map = HImap.init( 'map-canvas' );
//
//  current_markers = HImap.drawMarkers( map, viewpoints );
//  current_flightPath = HImap.drawFlightPath( map, viewpoints );
//  HImap.suitBounds( map, viewpoints );
//}

//  function switchToDay(day) {
//    HImap.clearMarkers(current_markers);
//    HImap.clearFlightPath(current_flightPath);
//
//    current_markers = HImap.drawMarkers(map, dayTourCoords[day - 1]);
//    current_flightPath = HImap.drawFlightPath(map, dayTourCoords[day - 1]);
//    HImap.suitBounds(map, dayTourCoords[day - 1]);
//    // map.panToBounds(bounds);
//    // google.maps.event.addListenerOnce(map, 'idle', function() {
//    //     map.fitBounds(bounds);
//    // });
//  }