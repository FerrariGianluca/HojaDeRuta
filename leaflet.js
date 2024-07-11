function exit() {
    window.location.href = 'index.php';
}

// Inicializa el mapa con valores predeterminados
var latPredeterminado = -34.628409927244306; // Latitud predeterminada
var lonPredeterminado = -58.36988252397463; // Longitud predeterminada
var defaultZoomLevel = 12; // Nivel de zoom predeterminado
var markerZoomLevel = 20;
var points = [];
var markers = {};
var currentMarker = null;
var areaLayer = null;
var originalBounds = null;

var map = L.map('map').setView([latPredeterminado, lonPredeterminado], defaultZoomLevel);

// Añade una capa de mapa (usando el mapa de OpenStreetMap en este caso)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Obtiene la ubicación actual del usuario
if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        updateMap(lat, lon, "Ubicación actual");
    }, function() {
        // No se pudo obtener la ubicación actual
        updateMap(latPredeterminado, lonPredeterminado, "GCBA");
    });
} else {
    // La geolocalización no es compatible con este navegador
    updateMap(latPredeterminado, lonPredeterminado, "GCBA");
}

function updateMap(lat, lon, name) {
    // Asegúrate de que las coordenadas sean válidas
    if (lat && lon) {
        points.push({ lat: lat, lon: lon, name: name });

        // Establece la vista del mapa en las coordenadas proporcionadas
        map.setView([lat, lon], defaultZoomLevel);

        var marker = L.marker([lat, lon]).addTo(map);
        markers[`${lat},${lon}`] = marker;
        marker.bindPopup(getPopupContent(lat, lon));

        fitMarkers(); // Ajusta los límites después de agregar el marcador
        
    } else {
        console.error("Coordenadas inválidas: ", lat, lon);
    }
}

function showError(message) {
    document.getElementById('error-message').textContent = message;
}

function clearError() {
    document.getElementById('error-message').textContent = '';
}

function isValidCoordinate(lat, lon) {
    lat = parseFloat(lat);
    lon = parseFloat(lon);

    if (isNaN(lat) || isNaN(lon)) {
        showError("Formato de número inválido.");
        return false;
    }

    if (lat < -90 || lat > 90 || lon < -180 || lon > 180) {
        showError("Coordenadas fuera de rango.");
        return false;
    }

    const latDecimals = (lat.toString().split('.')[1] || '').length;
    const lonDecimals = (lon.toString().split('.')[1] || '').length;

    if (latDecimals > 14 || lonDecimals > 14) {
        showError("Demasiados decimales en las coordenadas.");
        return false;
    }

    clearError();
    return true;
}

function addPoint() {
    var lat = document.getElementById('lat').value;
    var lon = document.getElementById('lon').value;
    var name = document.getElementById('des').value;

    if (isValidCoordinate(lat, lon) && name.trim() !== "") {
        lat = parseFloat(lat);
        lon = parseFloat(lon);

        points.push({ lat: lat, lon: lon, name: name });

        var marker = L.marker([lat, lon]).addTo(map);
        markers[`${lat},${lon}`] = marker;
        marker.bindPopup(getPopupContent(lat, lon));

        fitMarkers(); // Llama a fitMarkers después de agregar el marcador
    }
}

function getPopupContent(lat, lon) {
    var point = points.find(p => p.lat === lat && p.lon === lon);
    var buttonContent = currentMarker && currentMarker.lat === lat && currentMarker.lon === lon ? "Volver" : "Ir";

    if (point) {
        return `
            <div class="custom-popup">
                <strong>${point.name}</strong><br>
                <button onclick="zoomToMarker(${lat}, ${lon})">${buttonContent}</button>
            </div>
        `;
    } else {
        // En caso de que no se encuentre el punto, proporciona un contenido de reserva
        return `
            <div class="custom-popup">
                <strong>Punto desconocido</strong><br>
                <button onclick="zoomToMarker(${lat}, ${lon})">${buttonContent}</button>
            </div>
        `;
    }
}

function zoomToMarker(lat, lon) {
    if (currentMarker && currentMarker.lat === lat && currentMarker.lon === lon) {
        map.fitBounds(originalBounds);
        currentMarker = null;
    } else {
        map.setView([lat, lon], markerZoomLevel);
        currentMarker = { lat: lat, lon: lon };
    }

    for (var key in markers) {
        if (markers.hasOwnProperty(key)) {
            var marker = markers[key];
            var markerLatLng = marker.getLatLng();
            (function(marker, markerLatLng) {
                setTimeout(() => {
                    marker.getPopup().setContent(getPopupContent(markerLatLng.lat, markerLatLng.lng));
                }, 0);
            })(marker, markerLatLng);
        }
    }

    if (currentMarker) {
        setTimeout(() => {
           markers[`${currentMarker.lat},${currentMarker.lon}`].openPopup();
        }, 0);
    }
}

function fitMarkers() {
    var bounds = L.latLngBounds();
    var hasPoints = false;

    for (var key in markers) {
        if (markers.hasOwnProperty(key)) {
            var marker = markers[key];
            bounds.extend(marker.getLatLng());
            hasPoints = true;
        }
    }

    if (hasPoints) {
        map.fitBounds(bounds);
        originalBounds = bounds;

        if (areaLayer) {
            map.removeLayer(areaLayer);
        }
        areaLayer = L.rectangle(bounds, {color: "#ff7800", weight: 1}).addTo(map);
    } else {
        // Si no hay puntos, establece la vista en la ubicación predeterminada
        map.setView([latPredeterminado, lonPredeterminado], defaultZoomLevel);
    }
}

// Inicializa el mapa con los marcadores existentes
points.forEach((point) => {
    var marker = L.marker([point.lat, point.lon]).addTo(map);
    
    markers[`${point.lat},${point.lon}`] = marker;
    marker.bindPopup(getPopupContent(point.lat, point.lon));
});

// Ajusta los límites después de inicializar el mapa
fitMarkers();
