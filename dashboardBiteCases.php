<?php
session_start();
$active_tab = $_GET['active-tab'];

require_once("class/cases.php");
require_once("class/barangay.php");

// Check if the user is logged in and has admin privileges (userType = 1)
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
$brgyID = isset($_SESSION['user']['brgyID']) ? $_SESSION['user']['brgyID'] : '';
$user = $_SESSION['user'];
$name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : '';

$barangay = new Barangay();
$result1 = $barangay->getBrgyName($brgyID);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["verify"]) || isset($_POST["reject"])) {
        $caseID = $_POST["caseID"];
        $caseStatus = isset($_POST["verify"]) ? 1 : 2; // 1 for verified, 2 for not verified

        $case = new Cases();
        $result = $case->updateBiteCaseStatus($caseID, $caseStatus);

        if ($result === true) {
            // Successfully updated Bite Case status
            if ($caseStatus == 2) {
                // Redirect back immediately for status 2 (not verified)
                header("Location: ./dashboardBiteCases.php?active-tab=1");
                exit();
            }

            // Redirect for status 1 (verified)
            echo '<script>alert("Bite Case status updated successfully."); window.location.href = "proccess_viewBitesCaseLocation.php?caseID=' . $caseID . '";</script>';
        } else {
            // Failed to update Bite Case status
            echo "Failed to update Bite Case status: " . $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard 2</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #004643;
            color: #fffffe;
        }

        .a {
            color:#fffffe;
        }

        .container {
            margin-top: 30px;
        }

        .tab-content {
            background-color: #e8e4e6;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .nav-link {
            color: #007bff;
        }

        .nav-link.active {
            color: #ffffff;
            background-color: #007bff;
        }

        .btn-view {
            background-color: #17a2b8;
            color: #ffffff;
        }

        .btn-accept {
            background-color: #28a745;
            color: #ffffff;
        }

        .btn-reject {
            background-color: #dc3545;
            color: #ffffff;
        }

        .btn-manage {
            margin-top: 10px;
            background-color: #f9bc60;
            color: #001e1d;
            border-color: #f9bc60;
        }

        .btn-manage:hover {
            background-color: #e16162;
            border-color: #e16162;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg">
        <div class="container">
        <form method="post" action="./BAOpetdashboard.php?active-tab=1">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                    <button class="navbar-brand" class="btn btn-lg">My Pet Dashboard</button>
                    <!-- <button class="navbar-brand" class="btn btn-lg"></button> -->
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
        <h1 class="text-center">Manage Bite Cases for Barangay: <?php echo $result1 ?></h1>
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 1) ? ' active' : '' ?>" href="#newBiteCase" data-bs-toggle="tab">New Bite Cases</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 2) ? ' active' : '' ?>" href="#validBiteCase" data-bs-toggle="tab">Valid Bite Cases</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 3) ? ' active' : '' ?>" href="#rejectedBiteCase" data-bs-toggle="tab">Rejected Bite Cases</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- New Bite Cases Tab -->
            <div class="tab-pane <?=($active_tab == 1) ? ' active show' : '' ?>" id="newBiteCase">
                <table class="table">
                <label for="newPetSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="newSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="newBtn" type="button">Search</button>
                    </div>
                    <thead>
                        <tr>
                            <th>Pet Name</th>
                            <th>Victim's Name</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="valid-n">
                        <?php
                        $case = new Cases();
                        $cases = $case->getAllNewBiteCase($brgyID);

                        while ($row = $cases->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['pname'] . '</td>';
                            echo '<td>' . $row['victimsName'] . '</td>';
                            // Input date as a string
                            $input_date = $row['date'];

                            // Convert the input date to a DateTime object
                            $date_obj = new DateTime($input_date);

                            // Format the date as "Month Day, Year"
                            $formatted_date = $date_obj->format("F j, Y");

                            // Print the formatted date
                            echo '<td>' . $formatted_date . '</td>';
                            echo '<td>' . $row['description'] . '</td>';
                    //         echo '<td>
                    //         <form method="post" action="proccess_viewBitesCaseLocation.php">
                    //            <input type="hidden" name="caseID" value="' . $row['caseID'] . '">
                    //            <button type="submit" name="accept" class="btn btn-view">View Location</button>
                    //        </form>
                    // </td>';
                            echo '<td>
                                    <form method="post" action="./dashboardBiteCases.php?active-tab=1">
                                        <input type="hidden" name="caseID" value="' . $row['caseID'] . '">
                                        <button type="submit" name="verify" class="btn btn-accept">Accept</button>
                                        <button type="submit" name="reject" class="btn btn-reject">Reject</button>
                                    </form>
                                </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Valid Bite Cases Tab -->
            <div  class="tab-pane <?=($active_tab == 2) ? ' active show' : '' ?>" id="validBiteCase">
                <table class="table">
                <label for="ValidSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="validSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="validBtn" type="button">Search</button>
                    </div>
                    <thead>
                        <tr>
                            <th>Pet Name</th>
                            <th>Victim's Name</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>View Location</th>
                            
                        </tr>
                    </thead>
                    <tbody id="valid-v">
                        <?php
                        $case = new Cases();
                        $cases = $case->getAllValidBiteCase($brgyID);

                        while ($row = $cases->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['pname'] . '</td>';
                            echo '<td>' . $row['victimsName'] . '</td>';
                            // Input date as a string
                            $input_date = $row['date'];

                            // Convert the input date to a DateTime object
                            $date_obj = new DateTime($input_date);

                            // Format the date as "Month Day, Year"
                            $formatted_date = $date_obj->format("F j, Y");

                            // Print the formatted date
                            echo '<td>' . $formatted_date . '</td>';
                            echo '<td>' . $row['description'] . '</td>';
                            echo '<td>
                            <form method="post" action="proccess_viewBitesCaseLocation.php">
                               <input type="hidden" name="caseID" value="' . $row['caseID'] . '">
                               <button type="submit" name="accept" class="btn btn-view">View Location</button>
                           </form>
                                 </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Rejected Bite Cases Tab -->
            <div  class="tab-pane <?=($active_tab == 3) ? ' active show' : '' ?>" id="rejectedBiteCase">
                <table class="table">
                <label for="rejPetSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="rejSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="rejBtn" type="button">Search</button>
                    </div>
                    <thead>
                        <tr>
                            <th>Pet Name</th>
                            <th>Victim's Name</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="valid-r">
                        <?php
                        $case = new Cases();
                        $cases = $case->getAllRejectedBiteCase($brgyID);

                        while ($row = $cases->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['pname'] . '</td>';
                            echo '<td>' . $row['victimsName'] . '</td>';
                            $input_date = $row['date'];

                            // Convert the input date to a DateTime object
                            $date_obj = new DateTime($input_date);

                            // Format the date as "Month Day, Year"
                            $formatted_date = $date_obj->format("F j, Y");

                            // Print the formatted date
                            echo '<td>' . $formatted_date . '</td>';
                            echo '</tr>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <form method="POST" action="./dashboard1.php?active-tab=1" id="reportDeathCaseForm">
            <input type="hidden" name="residentID" value="<?php echo $brgyID; ?>">
            <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
            <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
            <button type="submit" class="btn btn-primary">Back</button>
        </form>
            <a href="logout.php" class="btn btn-primary btn-manage">Logout</a>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#newBtn").click(function() {
                var searchValue = $("#newSrch").val().toLowerCase();
                $("#valid-n tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });

            $("#validBtn").click(function() {
                var searchValue = $("#validSrch").val().toLowerCase();
                $("#valid-v tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });

            $("#rejBtn").click(function() {
                var searchValue = $("#rejSrch").val().toLowerCase();
                $("#valid-r tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });
        });
    </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
