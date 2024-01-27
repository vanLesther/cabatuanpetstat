<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the user's information from the session
$user = $_SESSION['user'];
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] != 1) {
    header("Location: login.php");
    exit();
}
if (isset($_POST['name'])) {
    // Retrieve the brgyID
    $name = $_POST['name'];

    // Now, you can use $brgyID as needed
} 
if (isset($_POST['residentID'])) {
    // Retrieve the brgyID
    $residentID = $_POST['residentID'];

    // Now, you can use $brgyID as needed
} 
if (isset($_POST['brgyID'])) {
    // Retrieve the brgyID
    $brgyID = $_POST['brgyID'];

    // Now, you can use $brgyID as needed
} 
if (isset($_POST['userType'])) {
    // Retrieve the brgyID
    $userType = $_POST['userType'];

    // Now, you can use $brgyID as needed
} 
$user = $_SESSION['user'];
$name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : '';
$brgyID = isset($_SESSION['user']['brgyID']) ? $_SESSION['user']['brgyID'] : '';
$residentID = isset($_SESSION['user']['residentID']) ? $_SESSION['user']['residentID'] : '';
$userType = isset($_SESSION['user']['userType']) ? $_SESSION['user']['userType'] : '';
$name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #007bff;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
        <div class="container">
        <form method="post" action="BAOpetdashboard.php">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                    <button class="navbar-brand" class="btn btn-lg">My Pet Dashboard</button>
                    <!-- <button class="navbar-brand" class="btn btn-lg"><?php echo isset($user['name']) ? $user['name'] : ''; ?></button> -->
                </form>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                <form method="post" action="#">
                <h4><?php echo isset($user['name']) ? $user['name'] : ''; ?></h4>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Report a Case</h1>
        <div class="mb-3">
            <form method="POST" action="Vcase_heatmaps.php" id="heatmapCase">
                <button type="submit" class="btn btn-primary btn-lg">View Case Heatmap</button>
            </form>
        </div>
        <div class="mb-3">
            <form method="POST" action="viewPetHeatmap.php" id="heatmapPet">
                <button type="submit" class="btn btn-primary btn-lg">View Pet Heatmap</button>
            </form>
        </div>
        <div class="mb-3">
            <form method="POST" action="dashboard1.php" id="heatmapPet">
                <button type="submit" class="btn btn-primary btn-lg">Back</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

