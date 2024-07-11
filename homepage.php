<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="styles_leaflet.css" />
</head>
<body>
    <div class="point-container">
        <div class="label-container">
            <label>Latitud
                <input type="text" id="lat" class="point-input">
            </label>
            <label>Longitud
                <input type="text" id="lon" class="point-input">
            </label>
            <label>Lugar
                <input type="text" id="des" class="point-input">
            </label>
        </div>
        <button type="button" class="point-btn" onclick="addPoint()">Agregar</button>
        <div id="error-message"></div>
    </div>
    <div class="points-list-container">
        <h2>Lista de ubicaciones</h2>
        <ul id="points-list" class="points-list">

        </ul>
    </div>
    <div class="map-container">
        <div id="map"></div>
    </div>
    <div class="btn-exit-container">
        <button class="btn-exit" onclick="exit()">Salir</button>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="leaflet.js"></script>
</body>
</html>