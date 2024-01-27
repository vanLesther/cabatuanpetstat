<?php
require_once("class/resident.php");
require_once("class/barangay.php");
require_once("class/db_connect.php");

$resident = new Resident();
$barangay = new Barangay();

$users = null;
$officers = null;

$brgyID = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['selectedBarangay'])) {
        $selectedBarangay = $_POST['selectedBarangay'];
        $brgyID = $barangay->getBrgyID($selectedBarangay);
    }
}

// Fetch residents and officers based on the selected barangay
$users = ($brgyID !== null) ? $resident->getAllValidResidentByBarangay($brgyID) : null;
$officers = ($brgyID !== null) ? $resident->getOfficersByBarangay($brgyID) : null;
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assign Officer</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Add Bootstrap JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
    <div class="container">
        <div class="d-flex justify-content-start">
            <a href="dashboardMAO.php" class="btn btn-primary">Back</a>
        </div>
            <h1>Assign Officer</h1>
        <form method="POST" action="" id="assignForm" name="assignForm">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#ValidResidents" data-bs-toggle="tab">Residents</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#Officers" data-bs-toggle="tab">Officers</a>
                </li>
            </ul>
            <label for="barangay" class="form-label">Barangay:</label>
            <select id="barangay" class="form-select" name="selectedBarangay" required>
                <option value="">Select Barangay</option>
                <?php
                // $specBrgy = $barangay->specBrg();
                // if(isset($_GET['brgyID'])){
                    
                // }else{
                    $brgys = $barangay->getBrgys();
                    foreach ($brgys as $brgy) {
                        echo '<option value="' . $brgy[0] . '">' . $brgy[2] . '</option>';
                    }
                // }
                ?>
            </select> 
            <!-- <select class="form-select" name="barangay" id="barangay" required>
                    <option value="">Select Owner</option>
                    <?php
                    // PHP code to fetch and display owner names from the database
                    // global $conn;

                    // if ($conn->connect_error) {
                    //     die("Connection failed: " . $conn->connect_error);
                    // }

                    // $sql1 = "SELECT barangay FROM barangay";
                    // $result1 = $conn->query($sql1);

                    // if ($result1->num_rows > 0) {
                    //     while ($row = $result1->fetch_assoc()) {
                    //         echo '<option value="' . $row["barangay"] . '">' . $row["barangay"] . '</option>';
                    //     }
                    // } else {
                    //     echo '<option value="">No owners found</option>';
                    // }
                    ?>
                </select> -->
        </form>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Valid Residents -->
            <div class="tab-pane fade show active" id="ValidResidents">
                <table class="table">
                    <thead>
                    <tr>
                        <label for="residentSearch" class="form-label"></label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="residentSearch"
                                placeholder="Search by name or email">
                            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button>
                        </div>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Assign</th>
                    </tr>
                    </thead>
                    <tbody id="valid-r">
                    <?php
                    if ($users && $users->num_rows > 0) {
                        while ($row = $users->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>
                            <form method="POST" action="process_assign.php">
                                <input type="hidden" name="residentID" value="' . $row['residentID'] . '">
                                <input type="hidden" name="brgyID" value="' . $row['brgyID'] . '">
                                <input type="hidden" name="brgyid" value="' . $brgyid . '">
                                <button type="submit" name="Assign" class="btn btn-success">Assign</button>
                            </form>
                                </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3">Select Barangay to display Residents.</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>

           <!-- Officers -->
        <div class="tab-pane fade" id="Officers">
            <table class="table">
                <thead>
                <label for="officerSearch" class="form-label"></label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="officerSearch"
                                placeholder="Search by name or email">
                            <button class="btn btn-primary" id="officerSearchBtn" type="button">Search</button>
                        </div>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="valid-o">
                    <?php
                    if ($officers && $officers->num_rows > 0) { 
                        while ($row = $officers->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>
                                <form method="POST" action="process_revoke.php">
                                    <input type="hidden" name="residentID" value="' . $row['residentID'] . '">
                                    <input type="hidden" name="brgyID" value="' . $row['brgyID'] . '">
                                    <button type="submit" name="Revoke" class="btn btn-danger">Revoke</button>
                                </form>
                            </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="2">Select Barangay to display Officers.</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>



        <script>
            $("#residentSearchBtn").click(function () {
                var searchValue = $("#residentSearch").val().toLowerCase();
                $("#valid-r tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });

         
            $("#officerSearchBtn").click(function () {
                var searchValue = $("#officerSearch").val().toLowerCase();
                $("#valid-o tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });
        </script>
        <script>
            $(document).ready(function() {
            // Update residents and officers when barangay is selected
            $("#barangay").on("change", function () {
                var selectedBrgyID = $(this).val();
                $.post('ajax/getResidentsByBrgy.php', {brgyid: selectedBrgyID}).done(function (data) {
                    $("#valid-r").html(data);
                });

                $.post('ajax/getOfficersByBrgy.php', {brgyid: selectedBrgyID}).done(function (data) {
                    $("#valid-o").html(data);
                });
            });
        });
        </script>
    </div>
    </body>
    </html>