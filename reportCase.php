<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST['brgyID'])) {
    // Retrieve the brgyID
    $brgyID = $_POST['brgyID'];

    // Now, you can use $brgyID as needed
} else {
    // Handle the case when brgyID is not set
    echo "BrgyID is not set in the POST data.";
}
if (isset($_POST['residentID'])) {
    // Retrieve the brgyID
    $residentID = $_POST['residentID'];

    // Now, you can use $brgyID as needed
} else {
    // Handle the case when brgyID is not set
    echo "residentID is not set in the POST data.";
}
if (isset($_POST['userType'])) {
    // Retrieve the brgyID
    $userType = $_POST['userType'];

    // Now, you can use $brgyID as needed
} else {
    // Handle the case when brgyID is not set
    echo "residentID is not set in the POST data.";
}
if (isset($_POST['name'])) {
    // Retrieve the brgyID
    $name = $_POST['name'];

    // Now, you can use $brgyID as needed
} 
// G
// Get the user's information from the session
$user = $_SESSION['user'];
$residentID = isset($_SESSION['user']['residentID']) ? $_SESSION['user']['residentID'] : '';
// Display the dashboard content
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
        <form method="post" action="BAOpetdashboard.php?active-tab=1">
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
            <form method="POST" action="addBiteCase.php" id="reportBiteCaseForm">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                <button type="submit" class="btn btn-primary btn-lg">Report Bite Case</button>
            </form>
        </div>
        <div class="mb-3">
        <form method="POST" action="addDeathCase.php" id="reportBiteCaseForm">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                <button type="submit" class="btn btn-primary btn-lg">Report Death Case</button>
            </form>
        </div>
        <div class="mb-3">
        <form method="POST" action="reportRabidBao.php" id="reportBiteCaseForm">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                <button type="submit" class="btn btn-primary btn-lg">Report Rabid Case</button>
            </form>
        </div>
        <div class="mb-3">
        <form method="POST" action="./dashboard1.php?active-tab=1" id="reportDeathCaseForm">
            <input type="hidden" name="residentID" value="<?php echo $brgyID; ?>">
            <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
            <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
            <button type="submit" class="btn btn-primary btn-lg">Back</button>
        </form>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

