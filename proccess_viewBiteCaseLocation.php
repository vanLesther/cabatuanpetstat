<?php
session_start();

require_once("class/cases.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $caseID = $_POST["caseID"];

    $case = new Cases();
    $result = $case->viewCaseLocation($caseID);
    $markerCode = "";

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          $geoID = $row['caseGeoID'];
          $lat = $row['latitude'];
          $lng = $row['longitude'];
          $name = $row['victimsName'];
          $barangay = $row['barangay'];
  
          $markerCode .= "var marker$geoID = L.marker([$lat, $lng]).addTo(map);\n";
          $markerCode .= "marker$geoID.bindPopup('Name: $name<br>Barangay: $barangay <br><form method=\"POST\" action=\"process_updateBiteCaseLocation.php\"><button type=\"submit\" name=\"geoID\" value=\"$geoID\" class=\"btn btn-primary\">Update Location</button></form>').openPopup();\n";
      }
      echo var_dump("$geoID");
  }
  
} else {  
    // Handle other cases if needed
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Location</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #map {
            height: 75vh;
            width: 75vw;
            margin: 20px auto;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .popup-btn {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mt-3 mb-4">Resident Location</h2>
        <div id="map"></div>
    </div>

    <script>
        var map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 20);

        L.tileLayer('https://api.maptiler.com/maps/basic-v2/{z}/{x}/{y}.png?key=A8yOIIILOal2yE0Rvb63', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
        }).addTo(map);

        <?php echo $markerCode; ?>
    </script>

    <script>
        function updateLocation(geoID) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_location.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.latitude && response.longitude) {
                        var newLat = response.latitude;
                        var newLng = response.longitude;
                        var updatedMarker = marker[geoID];
                        updatedMarker.setLatLng([newLat, newLng]);
                        updatedMarker.openPopup();
                        alert('Location updated successfully!');
                    } else {
                        alert('Failed to update location.');
                    }
                }
            };
            xhr.send('geoID=' + encodeURIComponent(geoID));
        }
    </script>
</body>

</html>
