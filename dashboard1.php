<?php
session_start();

$active_tab = $_GET['active-tab'];

require_once("class/resident.php");
require_once("class/barangay.php");
require_once("class/notification.php");

// Check if the user is logged in and has admin privileges (userType = 1)
$user = $_SESSION['user'];
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] != 1) {
    header("Location: login.php");
    exit();
}

$brgyID = isset($_SESSION['user']['brgyID']) ? $_SESSION['user']['brgyID'] : '';
$residentID = isset($_SESSION['user']['residentID']) ? $_SESSION['user']['residentID'] : '';
$userType = isset($_SESSION['user']['userType']) ? $_SESSION['user']['userType'] : '';
$name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : '';

$barangay = new Barangay();
$result = $barangay->getBrgyName($brgyID);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["accept"])) {
        $_POST["notifMessage"] = "Your account has been accepted.";
    } elseif (isset($_POST["reject"])) {
        $_POST["notifMessage"] = "Your account has been rejected.";
    } {
        $userID = $_POST["userID"];
        $status = isset($_POST["accept"]) ? 1 : 2; // 1 for accepted, 2 for rejected
        $notifType = isset($_POST["notif"]);
        // $notifMessage = isset($_POST["notifMessage"]);
         $notifDate = date('Y-m-d H:i:s');

        $resident = new Resident();
        $result = $resident->updateUserStatus($userID, $status);

        if ($result === true) {
            // Successfully updated user status
            if ($status == 2) {
                // Redirect back immediately for status 2 (rejected)
                $notif = new Notification();
                $notifDate = date('Y-m-d H:i:s');
                $push = $notif->addResidentStatus($userID, $notifDate, $notifMessage, $notifType);
                header("Location: ./dashboard1.php?active-tab=1");
                exit();
            }
            // Redirect for status 1 (accepted)
            $notif = new Notification();
           
            $push = $notif->addResidentStatus($userID, $notifDate, $notifMessage, $notifType);
            echo '<script>alert("User status updated successfully"); window.location.href = "proccess_viewResidentLocation.php?userID=' . $userID . '";</script>';
        } else {
            // Failed to update user status
            echo "Failed to update user status: " . $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard 1</title>
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
                    <input type="hidden" name="active-tab"  value="1">
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
        <h1 class="text-center">Manage Resident for Barangay: <?php echo $result ?></h1>
        <ul>
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 1) ? ' active' : '' ?>" href="#newResidents" data-bs-toggle="tab">New Residents</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 2) ? ' active' : '' ?>" href="#validResidents" data-bs-toggle="tab">Valid Residents</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 3) ? ' active' : '' ?>" href="#rejectedResidents" data-bs-toggle="tab">Rejected Residents</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- New Residents -->
            <div class="tab-pane <?=($active_tab == 1) ? ' active show' : '' ?>" id="newResidents">
            <label for="newPetSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="newSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="newBtn" type="button">Search</button>
                    </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="valid-n">
                        <?php
                        $resident = new Resident();
                        $users = $resident->getAllNewResidents($brgyID);

                        while ($row = $users->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>
                            <form method="post" action="./dashboard1.php?active-tab=1">
                                <input type="hidden" name="userID" value="' . $row['residentID'] . '">
                                <input type="hidden" name="notifType" id="notifType" value="5">
                                <input type="hidden" name="notifDate" id="notifDate" value="' . date('Y-m-d') . '">
                                <input type="hidden" name="notifMessage" id="notifMessage" value="">
                                <button type="submit" name="accept" class="btn btn-accept">Accept</button>
                                <button type="submit" name="reject" class="btn btn-reject">Reject</button>                                                                       
                            </form>
                        </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Valid Residents -->
            <div class="tab-pane <?=($active_tab == 2) ? ' active show' : '' ?>" id="validResidents">
            <label for="ValidSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="validSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="validBtn" type="button">Search</button>
                    </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>View Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="valid-v"> 
                        <?php
                        $resident = new Resident();
                        $users = $resident->getAllValidResidents($brgyID);

                        while ($row = $users->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>
                                     <form method="post" action="proccess_viewResidentLocation.php">
                                        <input type="hidden" name="userID" value="' . $row['residentID'] . '">
                                        <button type="submit" name="accept" class="btn btn-view">View Location</button>
                                    </form>
                             </td>';
                            echo '<td>
                                <form method="post" action="addPetByBao.php">
                                    <input type="hidden" name="residentID" value="' . $row["residentID"] . '">
                                    <input type="hidden" name="userType" value="' . $row["userType"] . '">
                                    <input type="hidden" name="brgyID" value="' . $row["brgyID"] . '">
                                    <button type="submit" class="btn btn-primary">Add Pet</button>
                                </form>
                            </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Rejected Residents -->
            <div class="tab-pane <?=($active_tab == 3) ? ' active show' : '' ?>" id="rejectedResidents">
            <label for="rejPetSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="rejSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="rejBtn" type="button">Search</button>
                    </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="valid-r">
                        <?php
                        $resident = new Resident();
                        $users = $resident->getAllRejectedResidents($brgyID);

                        while ($row = $users->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <form method="POST" action="reportCase.php" id="reportBiteCaseForm" style="display: inline;">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <button type="submit" class="btn btn-manage">Report Case</button>
            </form>
            <form method="post" action="tabularBAO.php" style="display: inline;">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                <input type="hidden" name="name" value="<?php echo $name; ?>">                
                <button type="submit" class="btn btn-manage">View Reports</button>
            </form>
            <form method="post" action="./dashboardBiteCases.php?active-tab=1" style="display: inline;">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <button type="submit" class="btn btn-manage">Manage Bite Cases</button>
            </form>
            <form method="post" action="./dashboardDeathCases.php?active-tab=1" style="display: inline;">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <button type="submit" class="btn btn-manage">Manage Death Cases</button>
            </form>
            <form method="post" action="./dashboardRabidCases.php?active-tab=1" style="display: inline;">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="userType" value="<?php echo $userType; ?>">
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <button type="submit" class="btn btn-manage">Manage Suspected Cases</button>
            </form>
            <form method="post" action="./dashboard1pet.php?active-tab=1" style="display: inline;">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <button type="submit" class="btn btn-manage">Manage Pet</button>
            </form>
            <form method="post" action="createAccForResident.php" style="display: inline;">
                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <button type="submit" class="btn btn-manage">Create Account</button>
                    </form>
            <a href="viewHeatmaps.php" class="btn btn-manage">View Heatmaps</a>
        
            <a href="logout.php" class="btn btn-manage">Logout</a>
        </div>
    </div>  

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
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
</html>
<script>
    function switchToValidResidentsTab() {
        // Trigger a click event on the tab link associated with "validResidents"
        document.getElementById("validResidents").click();
        return false; // To prevent the form from submitting immediately
    }
</script>