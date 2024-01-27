<?php
session_start();
require_once("class/db_connect.php");
require_once("class/barangay.php");

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST['userType'])) {
    // Retrieve the brgyID
    $userType = $_POST['userType'];

    // Now, you can use $brgyID as needed
}
if (isset($_POST['name'])) {
    // Retrieve the brgyID
    $name = $_POST['name'];

    // Now, you can use $brgyID as needed
}
$brgyID = isset($_SESSION['user']['brgyID']) ? $_SESSION['user']['brgyID'] : '';
$residentID = isset($_SESSION['user']['residentID']) ? $_SESSION['user']['residentID'] : '';
$userType = isset($_SESSION['user']['userType']) ? $_SESSION['user']['userType'] : '';
$name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : '';

// Get the user's information from the session
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Bite Case Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add jQuery library -->

    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007bff;
        }
        #map,
        #mapUnknown {
            height: 300px;
        }
    </style>
    </style>
</head>

<body>
   
<div class="container mt-4">
<a href="reportCase.php?brgyID=<?php echo $brgyID; ?>&residentID=<?php echo $residentID; ?>&userType=<?php echo $userType; ?>"
                    class="btn btn-primary">Back</a>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#Known">Report Identified Pet</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#Unknown">Report Unidentified Pet</a>
        </li>
    </ul>
</div>

<div class="container">
        <div class="tab-content">
            <!-- Known Pet Section -->
            <div class="tab-pane fade show active" id="Known">

                <div class="mb-3">
                    <h1><i class="bi bi-journal"></i> Report Bite Case Form</h1>

                    <form method="POST" action="addBiteCase.php" id="searchForm">
                        <label for="petDescription" class="form-label">Search by Pet Description:</label>
                        <input type="text" name="pdescription" id="pdescription">
                        <label for="pname" class="form-label">Search by Pet Name:</label>
                        <input type="text" name="pname" id="pname">
                        <label for="name" class="form-label">Search by Owner's Name:</label>
                        <input type="text" name="name" id="name">
                        <button type="submit" name="search">Search</button>
                    </form>
                    <?php
                        global $conn;

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $searchTerm = isset($_POST['pdescription']) ? '%' . $_POST['pdescription'] . '%' : '';
                        $petNameTerm = isset($_POST['pname']) ? '%' . $_POST['pname'] . '%' : '';
                        $nameTerm = isset($_POST['name']) ? '%' . $_POST['name'] . '%' : '';

                        // If no search action or invalid search terms, provide a default query
                        if (!isset($_POST['search']) || ($searchTerm === '%' && $petNameTerm === '%' && $nameTerm === '%')) {
                            $defaultQuery = "SELECT DISTINCT p.petID, p.pname, p.pdescription, p.color, r.name, b.barangay, p.petType
                                            FROM pet p
                                            INNER JOIN resident r ON p.residentID = r.residentID
                                            INNER JOIN barangay b ON r.brgyID = b.brgyID
                                            WHERE p.status = 1";
                            
                            $defaultResult = $conn->query($defaultQuery);

                            if ($defaultResult->num_rows > 0) {
                                echo '<table class="table table-striped">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<th>Pet Name</th>';
                                echo '<th>Sex</th>';
                                echo '<th>Description</th>';
                                echo '<th>Owner</th>';
                                echo '<th>Color</th>';
                                echo '<th>Barangay</th>';
                                echo '<th>Action</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';

                                while ($row = $defaultResult->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['pname'] . '</td>';
                                    echo '<td>' . ($row['petType'] ? 'Male' : 'Female') . '</td>';
                                    echo '<td>' . $row['pdescription'] . '</td>';
                                    echo '<td>' . $row['name'] . '</td>';
                                    echo '<td>' . $row['color'] . '</td>';
                                    echo '<td>' . $row['barangay'] . '</td>';
                                    echo '<td>';
                                    echo '<form method="POST" action="#">';
                                    echo '<input type="hidden" name="petID" value="' . $row['petID'] . '">';
                                    echo '<button type="button" class="btn btn-primary reportPet" data-bs-toggle="modal" data-bs-target="#reportModal" data-petid="' . $row['petID'] . '">';
                                    echo 'Report';
                                    echo '</button>';
                                    echo '</form>';
                                    echo '</td>';
                                    echo '</tr>';
                                }

                                echo '</tbody>';
                                echo '</table>';
                            } else {
                                echo '<p>No pets found</p>';
                            }

                        } else { // If a search action is performed, display search results
                            $sql = "SELECT DISTINCT p.petID, p.pname, p.pdescription, p.color, r.name, b.barangay, p.petType
                                    FROM pet p
                                    INNER JOIN resident r ON p.residentID = r.residentID
                                    INNER JOIN barangay b ON r.brgyID = b.brgyID
                                    WHERE p.status = 1 AND p.pdescription LIKE ? AND p.pname LIKE ? AND name LIKE ?";
                            
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sss", $searchTerm, $petNameTerm, $nameTerm);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                echo '<table class="table table-striped">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<th>Pet Name</th>';
                                echo '<th>Sex</th>';
                                echo '<th>Description</th>';
                                echo '<th>Owner</th>';
                                echo '<th>Color</th>';
                                echo '<th>Barangay</th>';
                                echo '<th>Action</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                            
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['pname'] . '</td>';
                                    echo '<td>' . ($row['petType'] ? 'Male' : 'Female') . '</td>';
                                    echo '<td>' . $row['pdescription'] . '</td>';
                                    echo '<td>' . $row['name'] . '</td>';
                                    echo '<td>' . $row['color'] . '</td>';
                                    echo '<td>' . $row['barangay'] . '</td>';
                                    echo '<td>';
                                    echo '<form method="POST" action="#">';
                                    echo '<input type="hidden" name="petID" value="' . $row['petID'] . '">';
                                    echo '<button type="button" class="btn btn-primary reportPet" data-bs-toggle="modal" data-bs-target="#reportModal" data-petid="' . $row['petID'] . '">';
                                    echo 'Report';
                                    echo '</button>';
                                    echo '</form>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            
                                echo '</tbody>';
                                echo '</table>';
                            } else {
                                echo '<p>No pets found with the specified criteria</p>';
                            }
                        }
                        ?>
            </div>
            </div>

    <!-- Add this script to handle the data-petid when the Report button is clicked -->
    <script>
        // Update modal input fields when the Report button is clicked
        $('.reportPet').on('click', function () {
            var petID = $(this).data('petid');

            // Update the hidden input field for petID in the modal
            $('#petID').val(petID);

            // Update other hidden input fields if needed
            // $('#otherHiddenInput').val(someValue);

            // You can include more hidden input fields if necessary

            // Trigger the modal to open
            $('#reportModal').modal('show');
        });
    </script>

<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="addBiteCaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBiteCaseModalLabel">Report Bite</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="process_addBiteCase.php" id="reportBiteCaseForm">
                    <!-- Your existing form fields go here -->
                    <!-- Remove the hidden input field for petID -->
                    <div class="mb-3">
                        <label for="victimsName" class="form-label">Victim Name:</label>
                        <input type="text" class="form-control" name="victimsName" id="victimsName" required>
                    </div>
                    <input type="hidden" name="petID" id="petID" value="">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date & Time of Bite:</label>
                        <input type="datetime-local" class="form-control" name="date" id="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="bpartBitten" class="form-label">Body Part Bitten:</label>
                        <select class="form-control" name="bpartBitten" id="bpartBitten" required>
                            <option value="">Select Body Part</option>
                            <option value="0">Head and Neck Area</option>
                            <option value="1">Thorax Area</option>
                            <option value="2">Abdomen Area</option>
                            <option value="3">Upper Extremity Area</option>
                            <option value="4">Lower Extremity Area</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description:</label>
                        <input type="text" class="form-control" name="description" id="description" required>
                    </div>
                    <label class="form-label">Location:</label>
                    <div id="map"></div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var longitude = 122.48181;
                            var latitude = 10.87993;

                            // Initialize text elements
                            var latTextElement = document.getElementById('latText');
                            var lngTextElement = document.getElementById('lngText');

                            var map = L.map('map').setView([10.87993, 122.48181], 18);

                            L.tileLayer('https://api.maptiler.com/maps/dataviz/{z}/{x}/{y}@2x.png?key=A8yOIIILOal2yE0Rvb63', {
                                attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
                            }).addTo(map);

                            var marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);

                            // Update lat, lng, and text elements on marker drag
                            marker.on('dragend', function(e) {
                                var latLng = e.target.getLatLng();
                                var lat = latLng.lat.toFixed(6);
                                var lng = latLng.lng.toFixed(6);

                                // Set values in hidden form fields
                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = lng;

                                // Update text elements
                                latTextElement.innerText = 'Latitude: ' + lat;
                                lngTextElement.innerText = 'Longitude: ' + lng;
                            });
                        });
                        </script>

                    <div class="modal-footer">
                        <!-- Your existing hidden input fields go here -->
                        <input type="hidden" name="residentID" id="residentID" value="<?php echo $user['residentID']; ?>">
                        <input type="hidden" name="brgyID" id="brgyID" value="<?php echo $user['brgyID']; ?>">
                        <input type="hidden" name="caseType" id="caseType" value="0">
                        <input type="hidden" name="caseStatus" id="caseStatus" value="1">
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="submit" class="btn btn-primary" value="Report" onclick="getLocationAndSubmit()" form="reportBiteCaseForm">
                    </div>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
        <!-- Unknown Pet Section -->
        <div class="tab-pane fade" id="Unknown">
            <div class="mb-3">
                <h1><i class="bi bi-journal"></i> Report Bite Case Form</h1>

                <form method="POST" action="process_Unknown.php" id="addUnknown">
                    <div class="mb-3">
                        <label for="victimsName" class="form-label">Victim Name:</label>
                        <input type="text" class="form-control" name="victimsName" id="victimsName" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date & Time of Bite:</label>
                        <input type="datetime-local" class="form-control" name="date" id="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="bpartBitten" class="form-label">Body Part Bitten:</label>
                        <select class="form-control" name="bpartBitten" id="bpartBitten" required>
                            <option value="">Select Body Part</option>
                            <option value="0">Head and Neck Area</option>
                            <option value="1">Thorax Area</option>
                            <option value="2">Abdomen Area</option>
                            <option value="3">Upper Extremity Area</option>
                            <option value="4">Lower Extremity Area</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description:</label>
                        <textarea class="form-control" name="description" id="description" required></textarea>
                    </div>
                    <div id="mapUnknown"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var longitude = 122.48181;
    var latitude = 10.87993;

    // Initialize text elements
    var latTextElement = document.getElementById('latText');
    var lngTextElement = document.getElementById('lngText');

    var map = L.map('mapUnknown').setView([10.87993, 122.48181], 18);

    L.tileLayer('https://api.maptiler.com/maps/dataviz/{z}/{x}/{y}@2x.png?key=A8yOIIILOal2yE0Rvb63', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
        }).addTo(map);

    var marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);

    // Update lat, lng, and text elements on marker drag
    marker.on('dragend', function(e) {
        var latLng = e.target.getLatLng();
        var lat = latLng.lat.toFixed(6);
        var lng = latLng.lng.toFixed(6);

        // Set values in hidden form fields
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;


        // Update text elements
        latTextElement.innerText = 'Latitude: ' + lat;
        lngTextElement.innerText = 'Longitude: ' + lng;
    });
});
</script>
                    <input type="hidden" name="residentID" id="residentID" value="<?php echo $user['residentID']; ?>">
                    <input type="hidden" name="brgyID" id="brgyID" value="<?php echo $user['brgyID']; ?>">
                    <input type="hidden" name="caseType" id="caseType" value="0">
                    <input type="hidden" name="caseStatus" id="caseStatus" value="1">                    
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="submit" class="btn btn-primary" value="Add Bite Case" onclick="getLocationAndSubmit()" form="addUnknown">
                </form>

                <form method="POST" action="reportCase.php" id="reportUnknownCaseForm">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                    <button type="submit" class="btn btn-primary">Back</button>
                </form>
            </div>
        </div>
    </div>
            
    <!-- <script> 
   function getLocationAndSubmit() {
        getLocation();
        // Set the selected petID in the hidden input field
        document.getElementById('petID').value = document.getElementById('petName').value;
        // Assuming the form has an ID of "addBiteCaseForm"
        document.getElementById('addBiteCaseForm').submit();
        // Redirect to another page after submission
        window.location.href = 'process_addBiteCase.php';
    }

    function getLocation() {
        if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                // Geolocation is not supported by the browser
                // Handle the lack of support accordingly
            }
        }

        function showPosition(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            document.getElementById("latitude").value = latitude;
            document.getElementById("longitude").value = longitude;

            document.getElementById("reportBiteCaseForm").submit();
        }

        function showError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    // User denied permission
                    break;
                case error.POSITION_UNAVAILABLE:
                    // Location information is unavailable
                    break;
                case error.TIMEOUT:
                    // The request to get user location timed out
                    break;
                case error.UNKNOWN_ERROR:
                    // An unknown error occurred
                    break;
            }
        }
</script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
