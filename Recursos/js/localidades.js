// Initialize the map when the Google Maps API is loaded
function initMap() {
    // Coordinates for Instituto Tecnológico de Chihuahua II
    const itchiiCoordinates = { lat: 28.7084798, lng: -106.1044149 };
    
    // Create a new map centered on Instituto Tecnológico de Chihuahua II
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 17,
        center: itchiiCoordinates,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [
            {
                "featureType": "all",
                "elementType": "geometry",
                "stylers": [
                    { "color": "#242f3e" }
                ]
            },
            {
                "featureType": "all",
                "elementType": "labels.text.stroke",
                "stylers": [
                    { "lightness": -80 }
                ]
            },
            {
                "featureType": "administrative",
                "elementType": "labels.text.fill",
                "stylers": [
                    { "color": "#746855" }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.fill",
                "stylers": [
                    { "color": "#2b3544" }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.stroke",
                "stylers": [
                    { "color": "#212a37" }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    { "color": "#17263c" }
                ]
            }
        ]
    });
    
    // Create a marker for Instituto Tecnológico de Chihuahua II
    const marker = new google.maps.Marker({
        position: itchiiCoordinates,
        map: map,
        title: 'Instituto Tecnológico de Chihuahua II'
    });
    
    // Add info window to the marker
    const infoWindow = new google.maps.InfoWindow({
        content: `<div style="color: #000;">
            <strong>Instituto Tecnológico de Chihuahua II</strong><br>
            Av. de las Industrias 11101, Complejo Industrial Chihuahua<br>
            Chihuahua, Chih., México
        </div>`
    });
    
    // Open info window when marker is clicked
    marker.addListener('click', () => {
        infoWindow.open(map, marker);
    });
    
    // Open info window by default
    infoWindow.open(map, marker);
}