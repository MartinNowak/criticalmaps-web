<?php require("wrapper_head.php"); ?>

<div id="map"></div>

<script type="text/javascript">
    $().ready( function () {
	    var currentMarkers = [];
	
	    var bikeIcon = L.icon( {
	        iconUrl: '/assets/images/marker-bike.png',
	        iconSize: [24, 24],
	        iconAnchor: [12, 12]
	    } );
	
	    var bikeMap = new L.map( 'map', { zoomControl: false } ).setView( [52.468209, 13.425995], 3 );
	
	    L.tileLayer( 'http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
	        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
	    } ).addTo( bikeMap );
	
	    new L.Control.Zoom( { position: 'bottomleft' } ).addTo( bikeMap );
	    var hash = new L.Hash( bikeMap );
	
	    var saveHashToParent = function () {
	        if ( parent.criticalMapsMain && typeof parent.criticalMapsMain.saveMapState == 'function' ) {
	            parent.criticalMapsMain.saveMapState( location.hash );
	        }
	    }
	
	    bikeMap.on( "moveend", function () {
	        saveHashToParent()
	    }, this );
	
	    bikeMap.on( "zoomend", function () {
	        saveHashToParent()
	    }, this );
	
	    function setNewLocations( locationsArray ) {
	
	        //remove old markers
	        currentMarkers.forEach( function ( marker ) {
	            bikeMap.removeLayer( marker )
	        } );
	
	        //add new markes
	        locationsArray.forEach( function ( coordinate ) {
	            var marker = L.marker( [coordinate.latitude, coordinate.longitude], {icon: bikeIcon} ).addTo( bikeMap );
	            currentMarkers.push( marker );
	
	        } );
	    }
	
	    var refreshLocationsFromServer = function () {
	        $.getJSON( "//api.criticalmaps.net/postv2", function ( data ) {
	
	            locationsArray = [];
	
	            var locations = data.locations;
	
	            for ( var key in locations ) {
	                if ( locations.hasOwnProperty( key ) ) {
	                    var currentLocation = locations[key];
	                    var coordinate = {
	                        latitude: criticalMapsUtils.convertCoordinateFormat( currentLocation.latitude ),
	                        longitude: criticalMapsUtils.convertCoordinateFormat( currentLocation.longitude )
	                    }
	                    locationsArray.push( coordinate );
	                    console.log( "new coords: " + JSON.stringify( coordinate ) + " " + new Date().toString() );
	                }
	            }
	
	            setNewLocations( locationsArray );
	        } );
	    }
	    setInterval( function () { refreshLocationsFromServer() }, 20000 );
	
	    refreshLocationsFromServer();
	
	    $( "body" ).keypress( function ( event ) {
	        if ( event.which == 104 ) {
	            setInterval( function () { refreshLocationsFromServer() }, 1000 );
	            alert( "ab geht die post!" );
	        }
	    } );
    } );
</script>

<?php require("wrapper_footer.php"); ?>