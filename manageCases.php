<?php
session_start();

// Check if the user is logged in and has admin privileges (userType = 1)
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] != 1) {
    header("Location: login.php");
    exit();
}

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
<html>
<head>
    <title>Dashboard 2</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Manage Pets for Barangay: <?php echo isset($_SESSION['user']['barangay']) ? $_SESSION['user']['barangay'] : ''; ?></h1>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#newCases" data-bs-toggle="tab">New Cases</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#validCases" data-bs-toggle="tab">Valid Cases</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#rejectedCases" data-bs-toggle="tab">Rejected Cases</a>
            </li>
        </ul>
        
        <div class="tab-content">
        <!-- Pet Dashboard -->
        <div class="tab-pane fade show active" id="newPets">
            <table class="table">
                <thead>
                    <tr>
                        <th>Resident Name</th>
                        <th>Pet Name</th>
                        <th>Type</th>
                        <th>Sex</th>
                        <th>Color</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $pet = new Pet();
                    $pets = $pet->getAllNewPets($brgyID);                     
                    while ($row = $pets->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td>' . $row['pname'] . '</td>';
                        echo '<td>' . ($row['petType'] == 0 ? 'Dog' : 'Cat') . '</td>';
                        echo '<td>' . ($row['sex'] == 0 ? 'Male' : 'Female') . '</td>';
                        echo '<td>' . $row['color'] . '</td>';
                        echo '<td>
                                <form method="post" action="dashboard1pet.php">
                                    <input type="hidden" name="petID" value="' . $row['petID'] . '">
                                    <button type="submit" name="verify" class="btn btn-success">Accept</button>
                                    <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                                </form>
                            </td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Valid Pets -->
        <div class="tab-pane fade" id="validPets">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Resident Name</th>
                            <th>Pet Name</th>
                            <th>Type</th>
                            <th>Sex</th>
                            <th>Color</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $pet = new Pet();
                            $pets = $pet->getAllValidPets($brgyID);

                            while ($row = $pets->fetch_assoc()) {
                            echo '<tr>';
                                echo '<td>' . $row['name'] . '</td>';
                                echo '<td>' . $row['pname'] . '</td>';
                                echo '<td>' . ($row['petType'] == 0 ? 'Dog' : 'Cat') . '</td>';
                                echo '<td>' . ($row['sex'] == 0 ? 'Male' : 'Female') . '</td>';
                                echo '<td>' . $row['color'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="rejectedPets">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Resident Name</th>
                            <th>Pet Name</th>
                            <th>Type</th>
                            <th>Sex</th>
                            <th>Color</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $pet = new Pet();
                            $pets = $pet->getAllRejectedPets($brgyID);

                            while ($row = $pets->fetch_assoc()) {
                            echo '<tr>';
                                echo '<td>' . $row['name'] . '</td>';
                                echo '<td>' . $row['pname'] . '</td>';
                                echo '<td>' . ($row['petType'] == 0 ? 'Dog' : 'Cat') . '</td>';
                                echo '<td>' . ($row['sex'] == 0 ? 'Male' : 'Female') . '</td>';
                                echo '<td>' . $row['color'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        <a href="dashboard1.php" class="btn btn-primary">Manage User</a>
        <a href="updateVacStatus.php" class="btn btn-primary">Update Vaccination Status</a>
        <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
