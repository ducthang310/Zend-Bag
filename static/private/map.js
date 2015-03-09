$(document).ready(function () {

    var markers = [];
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;
    var pickup_address;
    var searchFrom;
    var delivery_address;
    var searchTo;
    var bounds;


    var rendererOptions = {
        draggable: true
    };

    initMap = function(canDrag){
        setMap(canDrag);
        google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
            setAddress(directionsDisplay.getDirections());
        });
        pickupAddress("pickup_address");
        deliveryAddress("delivery_address");

    }


    function initialize() {
        setMap(1);
        pickupAddress("pickup_address");
        deliveryAddress("delivery_address");
    }

    function deliveryAddress(id){
        delivery_address =(document.getElementById(id));
        searchTo = new google.maps.places.SearchBox((delivery_address));
        google.maps.event.addListener(searchTo, 'places_changed', function() {
            var places = searchTo.getPlaces();

            if (places.length == 0) {
                return;
            }
            for (var i = 0, marker; marker = markers[i]; i++) {
                marker.setMap(null);
            }
            bounds = new google.maps.LatLngBounds();
            for (var i = 0, place; place = places[i]; i++) {

                var marker = new google.maps.Marker({
                    map: map,
                    draggable:true,
                    position: place.geometry.location
                });
                markers.push(marker);
                bounds.extend(place.geometry.location);
                google.maps.event.addListener(marker, 'dragend', function()
                {
                    geocodePosition(marker.getPosition(), "#"+id);
                    //calcRoute();
                });

                google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
                    setAddress(directionsDisplay.getDirections());
                });
                calcRoute();
            }

            map.fitBounds(bounds);
        });
    }

    function pickupAddress(id){
        pickup_address =(document.getElementById(id));
        searchFrom = new google.maps.places.SearchBox((pickup_address));
        calcRoute();
        google.maps.event.addListener(searchFrom, 'places_changed', function() {
            var places = searchFrom.getPlaces();

            if (places.length == 0) {
                return;
            }
            for (var i = 0, marker; marker = markers[i]; i++) {
                marker.setMap(null);
            }
            calcRoute();

            bounds = new google.maps.LatLngBounds();
            for (var i = 0, place; place = places[i]; i++) {

                var marker = new google.maps.Marker({
                    map: map,
                    draggable:true,
                    title: place.name,
                    position: place.geometry.location
                });
                markers.push(marker);
                google.maps.event.addListener(marker, 'dragend', function()
                {
                    geocodePosition(marker.getPosition(), "#"+id);
                    //calcRoute();
                });
                google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
                    setAddress(directionsDisplay.getDirections());
                });
                bounds.extend(place.geometry.location);
            }

            map.fitBounds(bounds);
        });
    }
    function setMap(canDrag){
        if ($("#map-canvas").length > 0) {
            if(canDrag == 1){
                directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
            }
            else{
                directionsDisplay = new google.maps.DirectionsRenderer();
            }

            map = new google.maps.Map(document.getElementById('map-canvas'), {
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            var defaultBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(-33.8902, 151.1759),
                new google.maps.LatLng(-33.8474, 151.2631));
            map.fitBounds(defaultBounds);

            directionsDisplay.setMap(map);
        }
    }

    function setAddress(result) {
        $("#pickup_address").val(result.routes[0].legs[0].start_address);
        $("#delivery_address").val(result.routes[0].legs[0].end_address);
    }

    function calcRoute() {
        if(document.getElementById('pickup_address').value && document.getElementById('delivery_address').value) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            var start = document.getElementById('pickup_address').value;
            var end = document.getElementById('delivery_address').value;
            var request = {
                origin: start,
                destination: end,
                travelMode: google.maps.TravelMode.DRIVING
            };
            directionsService.route(request, function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                }
            });
        }
    }

    function geocodePosition(pos, id)
    {
        geocoder = new google.maps.Geocoder();
        geocoder.geocode
        ({
                latLng: pos
            },
            function(results, status)
            {
                if (status == google.maps.GeocoderStatus.OK)
                {
                    $(id).val(results[0].formatted_address);
                }
            }
        );
    }
    if(document.getElementById("map-canvas")){
        initialize();
    }
})