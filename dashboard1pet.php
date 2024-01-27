<?php
session_start();
require_once("class/pet.php");
require_once("class/barangay.php");

// Check if the user is logged in and has admin privileges (userType = 1)
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] != 1) {
    header("Location: login.php");
    exit();
}
if (isset($_POST['petID'])) {
    // Retrieve the brgyID
    $petID = $_POST['petID'];

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
$petID = isset($_SESSION['user']['petID']) ? $_SESSION['user']['petID'] : '';
$user = $_SESSION['user'];
$name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : '';

$barangay = new Barangay();
$result1 = $barangay->getBrgyName($brgyID);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["verify"]) || isset($_POST["reject"])) {
        $petID = $_POST["petID"];
        $status = isset($_POST["verify"]) ? 1 : 2; // 1 for verified, 2 for not verified

        $pet = new Pet();
        $result = $pet->updatePetStatus($petID, $status);

        if ($result === true) {
            // Successfully updated pet status
            echo '<script>alert("Pet status updated successfully.");</script>';
        } else {
            // Failed to update pet status
            echo "Failed to update pet status: " . $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard 2</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
        }

        .card {
            margin-top: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
        }

        .nav-link {
            color: #007bff;
        }

        .nav-link.active {
            color: #ffffff;
            background-color: #007bff;
        }

        .tab-content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn-accept {
            background-color: #28a745;
            color: #ffffff;
        }

        .btn-reject {
            background-color: #dc3545;
            color: #ffffff;
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
        <h1 class="text-center">Manage Pets for Barangay: <?php echo $result1 ?></h1>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#newPets" data-bs-toggle="tab">New Pets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#validPets" data-bs-toggle="tab">Valid Pets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#rejectedPets" data-bs-toggle="tab">Rejected Pets</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- New Pets -->
            <div class="tab-pane fade show active" id="newPets">
                <div class="card">
                    <div class="card-header">
                        New Pets
                    </div>
                    <div class="card-body">
                    <label for="newPetSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="newSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="newBtn" type="button">Search</button>
                    </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Resident Name</th>
                                    <th>Pet Name</th>
                                    <th>Type</th>
                                    <th>Sex</th>
                                    <th>Color</th>
                                    <th>Latest Vaccination</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="valid-n">
                                <?php
                                $pet = new Pet();
                                $pets = $pet->getAllNewPets($brgyID);

                                while ($row = $pets->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['name'] . '</td>';
                                    echo '<td><button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#petModal_' . $row['petID'] . '">' . $row['pname'] . '</button></td>';
                                    echo '<td>' . ($row['petType'] == 0 ? 'Dog' : 'Cat') . '</td>';
                                    echo '<td>' . ($row['sex'] == 0 ? 'Male' : 'Female') . '</td>';
                                    echo '<td>' . $row['color'] . '</td>';
                                    // modal of the pname or pet name
                                    echo '<div class="modal fade" id="petModal_' . $row['petID'] . '" tabindex="-1" aria-labelledby="petModalLabel_' . $row['petID'] . '" aria-hidden="true">';
                                    echo '  <div class="modal-dialog">';
                                    echo '    <div class="modal-content">';
                                    echo '      <div class="modal-header">';
                                    echo '        <h3 class="modal-title" id="petModalLabel_' . $row['petID'] . '">Pet Details: ' . $row['pname'] . '</h3>';
                                    echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                    echo '      </div>';
                                    echo '      <div class="modal-body">';
                                    echo '        <h4>Age: ' . ($row['age']) . '</h4>';
                                    echo '        <h4>Description: ' . $row['pdescription'] . '</h4>';
                                    echo '        <h4>Neutering Status: ' . $row['Neutering'] . '</h4>';
                                    echo '      </div>';
                                    echo '    </div>';
                                    echo '  </div>';
                                    echo '</div>';
                                    
                                    // formatted date of the current vaccination if available
                                    echo '<td>';
                                    if (!empty($row['currentVac'])) {
                                        $input_date = $row['currentVac'];
                                        $date_obj = new DateTime($input_date);
                                        $formatted_date = $date_obj->format("F j, Y");
                                        echo $formatted_date;
                                    }
                                    echo '</td>'; 
                                    
                                    echo '<td>' . ($row['statusVac'] == 0 ? 'Vaccinated' : 'Unvaccinated') . '</td>';
                                    echo '<td>
                                            <form method="post" action="./dashboard1pet.php?active-tab=1">
                                                <input type="hidden" name="petID" value="' . $row['petID'] . '">
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
                </div>
            </div>

            <!-- Valid Pets -->
            <div class="tab-pane fade" id="validPets">
                <div class="card">
                    <div class="card-header">
                        Valid Pets
                    </div>
                    <div class="card-body">
                    <label for="ValidSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="validSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="validBtn" type="button">Search</button>
                    </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Resident Name</th>
                                    <th>Pet Name</th>
                                    <th>Type</th>
                                    <th>Sex</th>
                                    <th>Color</th>
                                    <th>Current Vaccination</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="valid-v">
                                <?php
                                $pet = new Pet();
                                $pets = $pet->getAllValidPets($brgyID);

                                while ($row = $pets->fetch_assoc()) {
                                    echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td><button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#petModal_' . $row['petID'] . '">' . $row['pname'] . '</button></td>';
                            echo '<td>' . ($row['petType'] == 0 ? 'Dog' : 'Cat') . '</td>';
                            echo '<td>' . ($row['sex'] == 0 ? 'Male' : 'Female') . '</td>';
                            echo '<td>' . $row['color'] . '</td>';

                            // modal of the pname or pet name
                            echo '<div class="modal fade" id="petModal_' . $row['petID'] . '" tabindex="-1" aria-labelledby="petModalLabel_' . $row['petID'] . '" aria-hidden="true">';
                            echo '  <div class="modal-dialog">';
                            echo '    <div class="modal-content">';
                            echo '      <div class="modal-header">';
                            echo '        <h3 class="modal-title" id="petModalLabel_' . $row['petID'] . '">Pet Name: ' . $row['pname'] . '</h3>';
                            echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                            echo '      </div>';
                            echo '      <div class="modal-body">';
                            echo '        <h3>Pet Details:</h4>';
                            echo '        <h4>Age: ' . ($row['age']) . '</h4>';
                            echo '        <h4>Registration Date: ' . ($row['regDate']) . '</h4>';
                            echo '        <h4>Description: ' . $row['pdescription'] . '</h4>';
                            echo '        <h4>Neutering Status: ' . ($row['Neutering'] == 0 ? 'Not Neutered' : 'Neutered') . '</h4>';

                            $petID = $row['petID'];
                            $allVaccinationsQuery = "SELECT lastVaccination FROM vaccination WHERE petID = $petID";
                            $allVaccinationsResult = mysqli_query($conn, $allVaccinationsQuery);
                            
                            if ($allVaccinationsResult && mysqli_num_rows($allVaccinationsResult) > 0) {
                                echo '        <h4>Recent Vaccinations:</h4>';
                                echo '        <ul>';
                                while ($vaccinationRow = mysqli_fetch_assoc($allVaccinationsResult)) {
                                    $formattedDate = ($vaccinationRow['lastVaccination'] ? date('F j, Y', strtotime($vaccinationRow['lastVaccination'])) : 'Not Available');
                                    echo '          <li>' . $formattedDate . '</li>';
                                }
                                echo '        </ul>';
                            } else {
                                echo '        <h4>Last Vaccination Date: Not available</h4>';
                            }
                            
                            // Update Vaccination Form
                            echo '        <form method="post" action="process_updateVac.php">';
                            echo '            <input type="hidden" name="petID" value="' . $row['petID'] . '">';
                            echo '            <label for="updateDate">Add New Vaccination Date:</label>';
                            echo '            <input type="date" name="currentVac" id="currentVac"required>';
                            echo '            <button type="submit" name="update" class="btn btn-success">Submit</button>';
                            echo '        </form>';

                            echo '      </div>';
                            echo '    </div>';
                            echo '  </div>';
                            echo '</div>';
                                        // formatted date of the current vaccination
                                        $input_date = $row['currentVac'];
                                        $date_obj = new DateTime($input_date);
                                        $formatted_date = $date_obj->format("F j, Y");
                                        echo '<td>' . $formatted_date . '</td>'; 

                                        echo '<td>' . ($row['statusVac'] == 0 ? 'Vaccinated' : 'Unvaccinated') . '</td>';
                                        echo '</tr>';
                                        }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rejected Pets -->
            <div class="tab-pane fade" id="rejectedPets">
                <div class="card">
                    <div class="card-header">
                        Rejected Pets
                    </div>
                    <div class="card-body">
                    <label for="rejPetSrch" class="form-label"></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="rejSrch" placeholder="Search...">
                        <button class="btn btn-primary" id="rejBtn" type="button">Search</button>
                    </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Resident Name</th>
                                    <th>Pet Name</th>
                                    <th>Type</th>
                                    <th>Sex</th>
                                    <th>Color</th>
                                    <th>Vaccination Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="valid-r">
                                <?php
                                $pet = new Pet();
                                $pets = $pet->getAllRejectedPets($brgyID);

                                while ($row = $pets->fetch_assoc()) {
                                  echo '<tr>';
                                    echo '<td>' . $row['name'] . '</td>';
                                    echo '<td><button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#petModal_' . $row['petID'] . '">' . $row['pname'] . '</button></td>';
                                    echo '<td>' . ($row['petType'] == 0 ? 'Dog' : 'Cat') . '</td>';
                                    echo '<td>' . ($row['sex'] == 0 ? 'Male' : 'Female') . '</td>';
                                    echo '<td>' . $row['color'] . '</td>';
                                    // modal of the pname or pet name
                                    echo '<div class="modal fade" id="petModal_' . $row['petID'] . '" tabindex="-1" aria-labelledby="petModalLabel_' . $row['petID'] . '" aria-hidden="true">';
                                    echo '  <div class="modal-dialog">';
                                    echo '    <div class="modal-content">';
                                    echo '      <div class="modal-header">';
                                    echo '        <h3 class="modal-title" id="petModalLabel_' . $row['petID'] . '">Pet Details: ' . $row['pname'] . '</h3>';
                                    echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                    echo '      </div>';
                                    echo '      <div class="modal-body">';
                                    echo '        <h4>Age: ' . ($row['age']) . '</h4>';
                                    echo '        <h4>Registration Date: ' . ($row['regDate']) . '</h4>';
                                    echo '        <h4>Description: ' . $row['pdescription'] . '</h4>';
                                    echo '      </div>';
                                    echo '    </div>';
                                    echo '  </div>';
                                    echo '</div>';
                                    //formatted date of the current vaccination
                                    $input_date = $row['currentVac'];
                                    $date_obj = new DateTime($input_date);
                                    $formatted_date = $date_obj->format("F j, Y");
                                    echo '<td>' . $formatted_date . '</td>'; 
                                    echo '<td>' . ($row['statusVac'] == 0 ? 'Vaccinated' : 'Unvaccinated') . '</td>';
                                    echo '<td>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="<?php echo ($userType == 1) ? './dashboard1.php?active-tab=1' : 'dashboard.php'; ?>" id="reportDeathCaseForm">
            <input type="hidden" name="residentID" value="<?php echo $brgyID; ?>">
            <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
            <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
            <button type="submit" class="btn btn-primary mt-3">Back</button>
        </form>
        <a href="logout.php" class="btn btn-primary mt-3">Logout</a>
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
