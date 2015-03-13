;

var HImap = {
	styles : [
        {
            "stylers": [
                { "saturation": -56 },
                { "gamma": 1.42 },
                { "hue": "#ffc300" },
                { "visibility": "simplified" }
            ]
        }, {
            "featureType": "water",
            "stylers": [
                { "hue": "#0066ff" },
                { "lightness": -14 },
                { "gamma": 1.79 },
                { "saturation": 38 }
            ]
        }
	],
	mapOptions : {
        zoom                  : 4,
        center                : { lat : 48.855, lng : 2.32 },
        scrollwheel           : false,
        streetViewControl     : false,
        mapTypeControl        : false
//        mapTypeControlOptions : {
//            mapTypeIds : [google.maps.MapTypeId.ROADMAP, 'map_style']
//        }
    },
	drawMarkers : function (map, coords) {
		var markers = [];
        for (var i = 0; i < coords.length; i++) {
            var viewpoint = coords[i];
            var myLatLng = new google.maps.LatLng(viewpoint[1], viewpoint[2]);
            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                icon: {
                    url: 'themes/public/images/site/map_ico.png',
                    size: new google.maps.Size(27, 32),
                    origin: new google.maps.Point(0, i * 32),
                    anchor: new google.maps.Point(14, 32)
                },
                title: viewpoint[0],
                zIndex: viewpoint[3]
            });
            markers.push(marker);
        }
        return markers;
	},
	clearMarkers : function (markers) {
		for (var i = 0; i < markers.length; i++) {
			markers[i].setMap(null);
		}
	},
	formatFlightPlanCoordinates : function (coords) {
		var flightPlanCoordinates = [];
		for (var i = 0; i < coords.length; i++) {
			flightPlanCoordinates.push(new google.maps.LatLng(coords[i][1], coords[i][2]));
		};
		return flightPlanCoordinates;
	},
	setFlightPath : function (flightPlanCoordinates) {
		return new google.maps.Polyline({
            path: flightPlanCoordinates,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2
        });
	},
	drawFlightPath : function (map, coords) {
		var flightPathCoords = this.formatFlightPlanCoordinates(coords);
        var fightPath = this.setFlightPath(flightPathCoords);
        fightPath.setMap(map);
        return fightPath;
	},
	clearFlightPath : function (flightPath) {
		flightPath.setMap(null);
	},
	setBounds : function (coords) {
        var i = coords.length;
        var bounds = new google.maps.LatLngBounds();
        while (i--) {
            bounds.extend(new google.maps.LatLng(coords[i][1], coords[i][2]));
        }
        return bounds;
	},
	suitBounds : function(map, coords) {
		var bounds = this.setBounds(coords);
        if (coords.length <= 1) {
            var latlng = {
                lat: parseFloat(coords[0][1]),
                lng: parseFloat(coords[0][2])
            };
            map.setZoom(15);
            map.setCenter(latlng);
        } else {
            map.fitBounds(bounds);
        }
	},
	loadGoogleMap : function () {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'http://maps.google.cn/maps/api/js?libraries=geometry&key=AIzaSyB062x7b2UUvRIMLRIHJ8rFaZXGSkca89c&sensor=false&' + 'callback=HImap.init';
        document.body.appendChild(script);
	},
	init: function (dom) {
        var styledMap = new google.maps.StyledMapType(this.styles, { name: "Styled Map" });
        var map = new google.maps.Map(document.getElementById(dom), this.mapOptions);
        map.mapTypes.set('map_style', styledMap);
        map.setMapTypeId('map_style');
        return map;
    }

}
