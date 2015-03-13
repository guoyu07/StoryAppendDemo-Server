directives.hiPickTicketMapDir = function() {
    return {
        scope       : {
            mapid   : '=',
            editing : '=',
            points  : '=',
            mapinfo : '=',
            actions : '='
        },
        replace     : true,
        restrict    : 'E',
        templateUrl : pathinfo.module_dir + 'hi_pick_ticket_map/hi_pick_ticket_map.html',
        controller  : function($scope) {
            $scope.inited = false;
            $scope.featureLayer = L.mapbox.featureLayer();
            $scope.init = function() {
                L.mapbox.accessToken = "pk.eyJ1IjoibmF0ZWN1aSIsImEiOiJrOU9OdTBFIn0.SSxx8lhLixaiWZhXzslJ0g";
                $scope.map = L.mapbox.map($scope.mapid, "natecui.ig5adgfm", {});
                L.control.scale().addTo($scope.map);

                $scope.map.on('zoomend', function(e) {
                    $scope.mapinfo.zoom = $scope.map.getZoom();
                    console.log('zoomend, current zoom: ' + $scope.mapinfo.zoom);
                });

                $scope.map.on('moveend', function(e) {
                    $scope.mapinfo.center = $scope.map.getCenter();
                    console.log('moveend, current center: ' + $scope.mapinfo.center);
                });

                $scope.featureLayer.on('ready', function() {
                    // featureLayer.getBounds() returns the corners of the furthest-out markers,
                    // and map.fitBounds() makes sure that the map contains these.
                    $scope.map.fitBounds($scope.featureLayer.getBounds());
                });

                $scope.inited = true;
            };

            $scope.save = function() {
                $scope.actions.save();
            };

            $scope.cancel = function() {
                $scope.actions.cancel();
            };

            $scope.$watch('editing', function(newValue, oldValue) {
                if(newValue == 1 && $scope.inited == false) {
                    $scope.init();
                }
            });

            $scope.$watch('points', function(newValue, oldValue) {
                if(newValue && newValue.length > 0) {
                    console.log('newValue: ' + newValue);

                    angular.forEach(newValue, function(value, key) {
                        var marker = L.marker(value, {
                            icon : L.mapbox.marker.icon({
                                'marker-symbol' : 'bus',
                                'marker-color'  : '#f86767'
                            })
                        });

                        $scope.featureLayer.addLayer(marker);
                        //              marker.addTo($scope.map);

                    });
                    $scope.featureLayer.addTo($scope.map);
                    $scope.map.fitBounds($scope.featureLayer.getBounds());
                } else {
                    // clear markers
                    $scope.featureLayer.clearLayers();

                }
            });
        }
    };
};

app.directive('hiPickTicketMap', directives.hiPickTicketMapDir);