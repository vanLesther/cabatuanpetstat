<?php
session_start();

require_once("class/resident.php");
require_once("class/geolocation.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $geoID = $_POST["geoID"];

    $residentLocation = new Geolocation();
    $result = $residentLocation->updateGeoLocation($geoID);
    $markerCode = "";

    if ($result) {
        foreach ($result as $row) {
            $geoID = $row['geoID'];
            $lat = $row['latitude'];
            $lng = $row['longitude'];
            $name = $row['name'];
            $barangay = $row['barangay'];

            $markerCode .= "var marker$geoID = L.marker([$lat, $lng], { draggable: true }).addTo(map);\n";
            $markerCode .= "marker$geoID.bindPopup('Name: $name<br>Barangay: $barangay <br><form method=\"POST\" action=\"process_confirmUpdatedResidentLocation.php\">";
            $markerCode .= "<input type=\"hidden\" name=\"geoID\" value=\"$geoID\">";
            $markerCode .= "<input type=\"hidden\" name=\"name\" value=\"$name\">";
            $markerCode .= "<input type=\"hidden\" name=\"barangay\" value=\"$barangay\">";
            $markerCode .= "<input type=\"text\" id=\"newLat$geoID\" name=\"latitude\" value=\"$lat\">";
            $markerCode .= "<input type=\"text\" id=\"newLng$geoID\" name=\"longitude\" value=\"$lng\">";
            $markerCode .= "<button type=\"button\" class=\"btn btn-primary\" onclick=\"updateLocation($geoID)\">Update Location</button></form>";

            // Display latitude and longitude
            $markerCode .= "<p id=\"latLng$geoID\">Latitude: $lat, Longitude: $lng</p>";

            $markerCode .= "').openPopup();\n";
        }
    } else {
        // Failed to update user status
        echo "Failed to update user status.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <style>
    #map {
      height: 800px;
    }
  </style>
</head>
<body>
  <div id="map"></div>
  <div id="latLngDisplay"></div>
  <script>
    var map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 20);

    L.tileLayer('https://api.maptiler.com/maps/basic-v2/{z}/{x}/{y}.png?key=A8yOIIILOal2yE0Rvb63', {
      attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
    }).addTo(map);

    <?php echo $markerCode; ?>

    function updateLocation(geoID) {
      var newLat = document.getElementById("newLat" + geoID).value;
      var newLng = document.getElementById("newLng" + geoID).value;
      var latLngDisplay = document.getElementById("latLng" + geoID);

      // Update the marker's position
      var marker = map._layers[geoID];
      marker.setLatLng([newLat, newLng]);

      // Update the latitude and longitude display
      latLngDisplay.textContent = "Latitude: " + newLat + ", Longitude: " + newLng;

      // You can also update the server with the new latitude and longitude here if needed.
    }
  </script>
</body>
</html>
