<?php
session_start();
require_once "class/db_connect.php";
require_once("class/barangay.php");
require_once("class/cases.php");

$barangay = new Barangay();
$case = new Cases();
$lat = 0;
$lng = 0;
$lat1 = 0;
$lng1 = 0;

if (isset($_POST['selectedBarangay'])) {
    $brgyID = $_POST['selectedBarangay'];

// First Function
$users = $case->getAllValidCaseByBarangay($brgyID);

if ($users && $users->num_rows > 0) {
    $heatmapData = [];

    while ($row = $users->fetch_assoc()) {
        // Check if 'latitude' and 'longitude' keys exist in $row before accessing them
        if (isset($row['latitude'], $row['longitude'])) {
            $lat = $row['latitude'];
            $lng = $row['longitude'];
            $heatmapData[] = [$lat, $lng]; // Push data to the array
        }
    }
}

$brgy = $barangay->getBrgyLocation($brgyID);

    if ($brgy && isset($brgy[0]['latitude'], $brgy[0]['longitude'])) {
        $lat1 = $brgy[0]['latitude'];
        $lng1 = $brgy[0]['longitude'];
    }


    // // Return the updated values as JSON
    // echo json_encode(['lat1' => $lat1, 'lng1' => $lng1]);
} else {
    // Handle the case when selectedBarangay is not set
    echo json_encode(['error' => 'Selected barangay not provided.']);
}

// Now you can use $lat, $lng, $lat1, and $lng1 outside of their respective scopes if needed.
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title>Vector Tiles in Leaflet JS</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.maptiler.com/maptiler-sdk-js/v1.1.1/maptiler-sdk.umd.js"></script>
  <link href="https://cdn.maptiler.com/maptiler-sdk-js/v1.1.1/maptiler-sdk.css" rel="stylesheet" />
  <script src="https://cdn.maptiler.com/leaflet-maptilersdk/v1.0.0/leaflet-maptilersdk.js"></script>
  <script src="https://unpkg.com/heatmap.js"></script>
  <script src="https://unpkg.com/leaflet.heat"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
  </style>
</head>
<body>
  <div class="container">
    <label for="barangay" class="form-label">Barangay:</label>
    <select id="barangay" class="form-select" name="selectedBarangay" required>
      <option value="">Select Barangay</option>
      <?php
      global $conn;
      $query = "SELECT brgyID, barangay FROM barangay";
      $result = mysqli_query($conn, $query);

      // Loop through the query results and generate options
      while ($row = mysqli_fetch_assoc($result)) {
        $brgyID = $row['brgyID'];
        $barangay = $row['barangay'];
        echo "<option value='$brgyID'>$barangay</option>";
      }
      // Release the result set
      mysqli_free_result($result);

      // Check for errors
      if (!$result) {
        die("Database query failed.");
      }
      ?>
    </select>
    <div class="mb-3">
            <form method="POST" action="viewHeatmaps.php" id="heatmapPet">
                <button type="submit" class="btn btn-primary btn-lg">Back</button>
            </form>
        </div>
  </div>
  <div id="map"></div>
  <script>
    const key = 'A8yOIIILOal2yE0Rvb63';
    
    // Use PHP to echo the latitude and longitude values
    const lat1 = <?php echo $lat1; ?>;
    const lng1 = <?php echo $lng1; ?>;
    
    const map = L.map('map').setView([lat1, lng1], 15);
    
    const mtLayer = L.maptilerLayer({
      apiKey: key,
      style: "8a85054c-5879-4e0b-b2f8-7f9564b6e3f8", //optional
    }).addTo(map);

    var heatmapData = [];

    // Create heatmap layer using Leaflet Heatmap Overlay plugin
    var heat = L.heatLayer(heatmapData, {
      radius: 20,
      blur: 30,
      maxZoom: 18,
      max: 0.6, // Remove concentrationFactor from here
    }).addTo(map);

    $(document).ready(function () {
      // Listen for changes in the select element
      $("#barangay").change(function () {
        // Get the selected value
        var selectedBarangay = $(this).val();

        // Create a form dynamically
        var form = $('<form action="Vcase_heatmaps.php" method="POST">' +
          '<input type="hidden" name="selectedBarangay" value="' + selectedBarangay + '">' +
          '</form>');

        // Append the form to the body and submit it
        $('body').append(form);
        form.submit();
      });
    });

    // Update heatmap data and redraw the layer when needed
    function updateHeatmapData(newData) {
      heatmapData = newData;
      heat.setLatLngs(newData).redraw();
    }

    <?php
    // JavaScript block with PHP values
    if (isset($heatmapData)) {
      echo "var initialData = " . json_encode($heatmapData) . ";";
      echo "updateHeatmapData(initialData);"; // Update the heatmap initially with fetched data
    }
    ?>
  </script>
</body>
</html>