<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the user's information from the session
$user = $_SESSION['user'];

// Display the dashboard content
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Pet Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add jQuery library -->

    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <script>
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

            // Assign the latitude and longitude values to hidden form fields
            document.getElementById("latitude").value = latitude;
            document.getElementById("longitude").value = longitude;

            // Submit the form
            document.getElementById("reportCaseForm").submit();
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
    </script>
</head>

<body>
    <div class="container">
        <h1>Report Case Form</h1>
        <form method="POST" action="process_addBiteCase.php" id="reportCaseForm">
            <div class="mb-3">
                <label for="vicName" class="form-label">Victim's Name:</label>
                <input type="text" class="form-control" name="name" id="vicName" required>
            </div>
            <div class="mb-3">
                <label for="petType" class="form-label">Pet Type:</label>
                <select class="form-select" name="petType" id="petType" required>
                    <option value="">Select Pet Type</option>
                    <option value="0">Dog</option>
                    <option value="1">Cat</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="sex" class="form-label">Sex:</label>
                <select class="form-select" name="sex" id="sex" required>
                    <option value="">Select Sex</option>
                    <option value="0">Male</option>
                    <option value="1">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color:</label>
                <input type="text" class="form-control" name="color" id="color" required>
            </div>
            <input type="hidden" name="residentID" id="residentID" value="<?php echo $user['residentID']; ?>">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <input type="button" onclick="getLocation()" value="Add Pet" class="btn btn-primary">
        </form>
    </div>

    <!-- Add Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
