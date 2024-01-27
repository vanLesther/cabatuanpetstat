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
    </style>
</head>

<body>
<nav class="navbar navbar-toggler-icon navbar-collapse bg-light-subtle">
        <div class="container">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                <form method="post" action="dashboard.php">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                    <h6>Profile:</h6>
                    <button class="navbar-brand" class="btn btn-lg"><?php echo isset($user['name']) ? $user['name'] : ''; ?></button>
                </form>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1><i class="bi bi-journal"></i> Report Death Case Form</h1>
        <form method="POST" action="process_addDeathCaseIndiv.php" id="reportCaseForm">
            <div class="mb-3">
                <label for="petName" class="form-label">Pet Name:</label>
                <select class="form-select" name="petID" id="petID" required>
                    <option value="">Select Pet</option>
                    <?php
                    global $conn;

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM pet  WHERE residentID = ? AND status =1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $residentID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row["petID"] . '">' . $row["pname"] . '</option>';
                        }
                    } else {
                        echo '<option value="">No pets found</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <input type="text" class="form-control" name="description" id="description" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date Occured:</label>
                <input type="date" class="form-control" name="date" id="date" required>
            </div>
            <input type="hidden" name="confirmedRabies" id="confirmedRabies" value="0">
            <input type="hidden" name="residentID" id="residentID" value="<?php echo $user['residentID']; ?>">
            <input type="hidden" name="brgyID" id="brgyID" value="<?php echo $user['brgyID']; ?>">
            <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
            <input type="hidden" name="caseType" id="caseType" value="1">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="notifType" id="notifType" value="3">
            <input type="hidden" name="notifDate" id="notifDate" value="<?php echo date('Y-m-d'); ?>">
            <input type="hidden" name="notifMessage" id="notifMessage" value="A death case has been recorded.">            
            <input type="hidden" name="longitude" id="longitude">
            <input type="submit" value="Add Death Case" class="btn btn-primary" btn-lg onclick="getLocation()">
        </form>
      <form method="POST" action="./dashboard.php?active-tab=3" id="reportBiteCaseForm">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                <button type="submit" class="btn btn-primary">Back</button>
        </form>
    </div>
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

            document.getElementById("latitude").value = latitude;
            document.getElementById("longitude").value = longitude;

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
    <!-- Add Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
