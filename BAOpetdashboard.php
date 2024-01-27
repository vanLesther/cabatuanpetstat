<?php
session_start();

$active_tab = $_GET['active-tab'];

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST['brgyID'])) {
    // Retrieve the brgyID
    $brgyID = $_POST['brgyID'];

    // Now, you can use $brgyID as needed
} 
// Get the user's information from the session
$user = $_SESSION['user'];
$brgyID = isset($_SESSION['user']['brgyID']) ? $_SESSION['user']['brgyID'] : '';
$residentID = isset($_SESSION['user']['residentID']) ? $_SESSION['user']['residentID'] : '';
$userType = isset($_SESSION['user']['userType']) ? $_SESSION['user']['userType'] : '';
$name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : '';
// Include the Pet class
require_once("class/pet.php");
require_once("class/cases.php");
require_once("class/notification.php");
$pet = new Pet();
$cases = new Cases();
$notif = new Notification();

// Get all pets belonging to the user
$pets = $pet->getPetsByResidentID($user['residentID']);
$bites = $cases->getBitesByResidentID($user['residentID']);
$death = $cases->getDeathByResidentID($user['residentID']);
$suspected = $cases->getSuspectedCase($user['residentID']);
// $allNotifs = $notif->getNotifications($brgyID);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>
<style>
.navbar-nav .nav-link.notification-button {
    cursor: pointer;
}

</style>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Pet Dashboard</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                <form method="post" action="./dashboard1.php?active-tab=1">
                <input type="hidden" name="residentID" value="<?php echo $user['residentID']; ?>">
                <input type="hidden" name="brgyID" id="brgyID" value="<?php echo $user['brgyID']; ?>">
                <input type="hidden" name="userType" id="userType" value="<?php echo $user['userType']; ?>">
                <button class="nav-link" class="btn btn-primary">Officer Dashboard</button>
            </form>
                </li>
                <li class="nav-item">
                    <input type="hidden" name="brgyID" id="brgyID" value="<?php echo $user['brgyID']; ?>">
                    <button class="nav-link" data-bs-toggle="modal" data-bs-target="#notificationModal">
                        Notifications
                    </button>
                </li>
                <li class="nav-item">
                <form method="post" action="logout.php">
                    <button class="nav-link" class="btn btn-primary">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
                

            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Welcome, <?php echo isset($user['name']) ? $user['name'] : ''; ?>!</h1>
        <p>Email: <?php echo isset($user['email']) ? $user['email'] : ''; ?></p>
    </div>

 <!-- Add Pet Section -->
<form method="POST" action="" id="petform" name="petform">
    <div class="container mt-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 1) ? ' active' : '' ?>" data-bs-toggle="tab" href="#addPets">Add Pet</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 2) ? ' active' : '' ?>" data-bs-toggle="tab" href="#bitePets">Report Bite Cases</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 3) ? ' active' : '' ?>" data-bs-toggle="tab" href="#deathPets">Report Death Cases</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=($active_tab == 4) ? ' active' : '' ?>" data-bs-toggle="tab" href="#suspectedPets">Report Suspected Cases</a>
            </li>
        </ul>
    </div>
</form>

<div class="tab-content">

    <!-- Add Pet Section -->
    <div class="tab-pane <?=($active_tab == 1) ? ' active show' : '' ?> fade show" id="addPets">
        <div class="container mt-4">
            <h2>Add Pet</h2>
            <form method="POST" action="addPetBao.php">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                    <button type="submit" class="btn btn-primary">Add Pet</button>
                </form>
        </div>

        <div class="container mt-4">
            <h4>My Pets:</h4>
            <label for="residentSearch" class="form-label"></label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="residentSearch" placeholder="Search by name or email">
                <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Pet's Name</th>
                        <th>Date of Registry</th>
                        <th>Pet Type</th>
                        <th>Sex</th>
                        <th>Color</th>
                        <th>Vaccination Status</th>
                        <th>Neutering</th>
                        <th>Current Vaccination</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="valid-a"> 
                <?php
                    foreach ($pets as $pet) {
                        ?>
                        <tr>
                        <td>
                            <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#petModal_<?php echo $pet['petID']; ?>">
                                <?php echo $pet['pname']; ?>
                            </button>
                            <td>
                                <?php
                                $input_date = new DateTime($pet['regDate']);
                                echo $input_date->format("F j, Y");
                                ?>
                            </td>
                            <td><?php echo ($pet['petType'] == 0) ? 'Dog' : 'Cat'; ?></td>
                            <td><?php echo ($pet['sex'] == 0) ? 'Male' : 'Female'; ?></td>
                            <td><?php echo $pet['color']; ?></td>
                            <td><?php echo ($pet['statusVac'] == 0) ? 'Vaccinated' : 'Unvaccinated'; ?></td>
                            <td><?php echo ($pet['Neutering'] == 0) ? 'Neutered' : 'Not Neutered'; ?></td>
                            <td>
                                <?php
                                $input_date = new DateTime($pet['currentVac']);
                                echo $input_date->format("F j, Y");
                                ?>
                                <!-- <div class="d-flex justify-content-end">
                                    <a href="#update" data-bs-toggle="modal" style="display: inline-block;" class="updatePet" data-petid="<?php echo $pet['petID']; ?>">
                                        <button type='button' class='btn btn-primary btn-sm'>
                                            <input type="hidden" name="petID" value="<?php echo $pet['petID']; ?>">
                                            <i class="bi bi-pencil-fill updatePet"></i> Edit
                                        </button>
                                    </a>
                                </div> -->
                            </td>
                            <td><?php echo $pet['age']; ?></td>
                            <td>
                                <?php if ($pet['status'] == 1) { ?>
                                    <i class="bi bi-check-circle text-success"></i> Verified
                                <?php } else if ($pet['status'] == 2) { ?>
                                    <i class="bi bi-x-circle text-danger"></i> Rejected
                                <?php } else { ?>
                                    <i class="bi bi-question-circle text-warning"></i> Not Verified
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($pet['status'] == 0) { ?>
                                    <a href="#edit" data-bs-toggle="modal" style="display: inline-block;" class="editPet" data-petid="<?php echo $pet['petID']; ?>">
                                        <button type='button' class='btn btn-warning btn-sm'>
                                            <input type="hidden" name="petID" value="<?php echo $pet['petID']; ?>">
                                            <i class="bi bi-pencil-fill editPet"></i> Edit
                                        </button>
                                    </a>
                                    <form method="post" action="process_cancelBAO.php" style="display: inline-block; margin-left: 5px;">
                                        <input type="hidden" name="petID" value="<?php echo $pet['petID']; ?>">
                                        <button type="submit" name="cancel_reg" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x"></i> <!-- X icon for Delete -->
                                        </button>
                                    </form>
                                <?php } else { ?>
                                    <button class="btn btn-sm" disabled>Reviewed</button>
                                <?php } ?>
                            </td>
                        </tr>

    <div class="modal fade" id="petModal_<?php echo $pet['petID']; ?>" tabindex="-1" aria-labelledby="petModalLabel_<?php echo $pet['petID']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="petModalLabel_<?php echo $pet['petID']; ?>">Pet Name: <?php echo $pet['pname']; ?></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Display pet vaccination history and update form -->
                    <?php
                    $petID = $pet['petID'];
                    $allVaccinationsQuery = "SELECT lastVaccination FROM vaccination WHERE petID = ?";
                    $stmt = mysqli_prepare($conn, $allVaccinationsQuery);
                    mysqli_stmt_bind_param($stmt, "i", $petID);
                    mysqli_stmt_execute($stmt);
                    $allVaccinationsResult = mysqli_stmt_get_result($stmt);

                    if ($allVaccinationsResult && mysqli_num_rows($allVaccinationsResult) > 0) {
                        echo '<h4>Recent Vaccinations:</h4>';
                        echo '<ul>';
                        while ($vaccinationRow = mysqli_fetch_assoc($allVaccinationsResult)) {
                            $formattedDate = ($vaccinationRow['lastVaccination'] ? date('F j, Y', strtotime($vaccinationRow['lastVaccination'])) :  'Not Available');
                            echo '<li>' . $formattedDate . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<h4>Last Vaccination Date: Not available</h4>';
                    }
                    ?>

                    <!-- Update Vaccination Form -->
                    <form method="post" action="process_updateVac.php">
                        <input type="hidden" name="userType" value="<?php echo $user['userType']; ?>">
                        <input type="hidden" name="petID" value="<?php echo $pet['petID']; ?>">
                        <!-- Other hidden inputs if needed -->
                        <label for="updateDate">Add New Vaccination Date:</label>
                        <input type="date" name="currentVac" id="currentVac" required>
                        <button type="submit" name="update" class="btn btn-success">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

                    <!-- Ensure that you close the table properly -->
                    </tbody>
                    </table>
                    </div>
                    </div>
        
        <script>
            // Update modal input fields when the Edit button is clicked
            $('.editPet').on('click', function () {
                var petID = $(this).data('petid');

                // Update the hidden input field for petID in the modal
                $('#petID').val(petID);

                // Trigger the modal to open
                $('#edit').modal('show');
            });
        </script>
        <!-- <script>
    // Update modal input fields when the Report button is clicked
    $('.updatePet').on('click', function () {
        var petID = $(this).data('petid');

        // Update the hidden input field for petID in the modal
        $('#petID').val(petID);

        // You can include more hidden input fields if necessary

        // Trigger the modal to open
        $('#update').modal('show');
    });
</script> -->
        <div id="edit" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Pet Details</h4>  
                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="process_editPet.php">
                        <input type="hidden" name="petID" id="petID" value="">
                            <div class="mb-2">
                                <label for="pname" class="form-label">Pet's Name:</label>
                                <input type="text" name="pname" value="<?php echo $pet['pname']; ?>" placeholder="Pet's Name" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label for="registryDate" class="form-label">Date of Registry:</label>
                                <input type="text" name="regDate" value="<?php $input_date = new DateTime($pet['regDate']);
                                                                        echo $input_date->format("F j, Y"); ?>" placeholder="Date of Registry" class="form-control" disabled>
                            </div>

                            <div class="mb-2">
                                <label for="petType" class="form-label">Pet Type:</label>
                                <select class="form-select" name="petType" id="petType" required>
                                    <option value=""><?php echo ($pet['petType'] == 0) ? 'Dog' : 'Cat'; ?></option>
                                    <option value="0">Dog</option>
                                    <option value="1">Cat</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="petSex" class="form-label">Sex:</label>
                                <select class="form-select" name="sex" id="sex" required>
                                    <option value=""><?php echo ($pet['sex'] == 0) ? 'Male' : 'Female'; ?></option>
                                    <option value="0">Male</option>
                                    <option value="1">Female</option>
                                </select>                        
                            </div>

                            <div class="mb-2">
                                <label for="color" class="form-label">Color:</label>
                                <input type="text" name="color" value="<?php echo $pet['color']; ?>" placeholder="Color" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label for="vaccinationStatus" class="form-label">Vaccination Status:</label>
                                <input type="text" name="statusVac" value="<?php echo $pet['statusVac'] == 0 ? 'Vaccinated' : 'Unvaccinated'; ?>" placeholder="Vaccination Status" class="form-control" disabled>
                            </div>

                            <div class="mb-2">
                                <label for="neuteringStatus" class="form-label">Neutering Status:</label>
                                <select class="form-select" name="neutering" id="neutering" required>
                                    <option value=""><?php echo ($pet['Neutering'] == 0) ? 'Neutered' : 'Not Neutered'; ?></option>
                                    <option value="0">Yes</option>
                                    <option value="1">No</option>
                                </select>                        
                            </div>

                            <div class="mb-2">
                                <label for="currentVaccination" class="form-label">Current Vaccination:</label>
                                <input type="text" name="currentVac" value="<?php $input_date = new DateTime($pet['currentVac']);
                                                                            echo $input_date->format("F j, Y"); ?>" placeholder="Current Vaccination" class="form-control" disabled>
                            </div>

                            <div class="mb-2">
                                <label for="petAge" class="form-label">Age:</label>
                                <input type="text" name="age" value="<?php echo $pet['age']; ?>" placeholder="Age" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                                <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                                <input type="submit" name="edit" value="Submit" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>   
            </div>
        </div>
     

<!--       
    <div id="update" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Vaccination</h4>  
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post" action="process_updateVac.php">
                    <input type="hidden" name="petID" id="petID" value="">
                    <?php
                        require_once("class/db_connect.php");

                    //     // Check if petID is set in the request
                    //     if (isset($_POST['petID'])) {
                    //         $petID = $_POST['petID'];

                    //         $allVaccinationsQuery = "SELECT lastVaccination FROM vaccination WHERE petID = $petID";
                    //         $allVaccinationsResult = mysqli_query($conn, $allVaccinationsQuery);
                        
                    //     if ($allVaccinationsResult && mysqli_num_rows($allVaccinationsResult) > 0) {
                    //         echo '<h4>Name of Pet: ' . $pet['petID'] . ' ' . $pet['pname'] . '</h4>';
                    //         echo '        <h4>Recent Vaccinations:</h4>';
                    //         echo '        <ul>';
                    //         while ($vaccinationRow = mysqli_fetch_assoc($allVaccinationsResult)) {
                    //             $formattedDate = ($vaccinationRow['lastVaccination'] ? date('F j, Y', strtotime($vaccinationRow['lastVaccination'])) : 'Not Available');
                    //             echo '          <li>' . $formattedDate . '</li>';
                    //         }
                    //         echo '        </ul>';
                    //     } else {
                    //         echo '        <h4>Last Vaccination Date: Not available</h4>';
                    //     }
                    // }
                        
                    //     // Update Vaccination Form
                    //     echo '        <form method="post" action="process_updateVac.php">';
                    //     // echo '            <input type="hidden" name="petID" value="' . $pet['petID'] . '">';
                    //     echo '            <label for="updateDate">Add New Vaccination Date:</label>';
                    //     echo '            <input type="date" name="currentVac" id="currentVac"required>';
                    //     echo '            <button type="submit" name="update" class="btn btn-success">Submit</button>';
                    //     echo '        </form>';

                    //     echo '      </div>';
                    //     echo '    </div>';
                    //     echo '  </div>';
                    //     echo '</div>';
                         
                        ?>

                        <div class="mb-2">
                            <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                            <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                            <input type="submit" name="edit" value="Submit" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>   
        </div>
    </div> -->
    <div class="tab-pane <?=($active_tab == 2) ? ' active show' : '' ?> fade" id="bitePets">
        <div class="container">
            <div class="container mt-4">
                <h2>Report Bite Case</h2>
                <form method="POST" action="addBiteCase.php">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                    <button type="submit" class="btn btn-primary">Report Bite Case</button>
                </form>
            </div>
        </div>

        <div class="container mt-4">
            <h4>Bite Reports:</h4>
            <label for="biteSearch" class="form-label"></label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="residentSearch" placeholder="Search by name or email">
                <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Pet's Name</th>
                        <th>Victim</th>
                        <th>Description</th>
                        <th>Date Occurred</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="valid-b">
                    <?php foreach ($bites as $cases) { ?>
                        <tr>
                            <td><?php echo $cases['pname']; ?></td>
                            <td><?php echo $cases['victimsName']; ?></td>
                            <td><?php echo $cases['description']; ?></td>
                            <td>
                                <?php  
                                $input_date = new DateTime($cases['date']);
                                echo $input_date->format("F j, Y, H:i:s");
                                ?>
                            </td>
                            <td>
                                <?php if ($cases['caseStatus'] == 0) { ?>
                                    <i class="bi bi-question-circle text-warning"></i> Not Verified
                                <?php } else if ($cases['caseStatus'] == 1) { ?>
                                    <i class="bi bi-check-circle text-success"></i> Verified
                                <?php } else { ?>
                                    <i class="bi bi-x-circle text-danger"></i> Rejected
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($cases['caseStatus'] == 1) { ?>
                                    <button class="btn btn-sm" disabled>Reviewed</button>
                                <?php } else { ?>
                                    <form method="post" action="process_cancelBAO.php">
                                        <input type="hidden" name="caseID" value="<?php echo $cases['caseID']; ?>">
                                        <button type="submit" name="cancel_bite" class="btn btn-danger">Cancel</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<div class="tab-pane <?=($active_tab == 3) ? ' active show' : '' ?> fade" id="deathPets">
        <div class="container mt-4">
        <h2>Report Death Case</h2>
        <form method="POST" action="addDeathCase.php">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                    <button type="submit" class="btn btn-primary">Report Death Case</button>
                </form>
        </div>

    <div class="container mt-4">
        <h4>Death Reports:</h4>
        <label for="residentSearch" class="form-label"></label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="residentSearch" placeholder="Search by name or email">
            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Pet's Name</th>
                    <th>Date Occurred</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="valid-d">
                <?php foreach ($death as $cases) { ?>
                    <tr>
                        <td><?php echo $cases['pname']; ?></td>
                        <td>
                            <?php  
                            $input_date = $cases['date'];
                            $date_obj = new DateTime($input_date);
                            $formatted_date = $date_obj->format("F j, Y");
                            echo $formatted_date;
                            ?>
                        </td>
                        <td><?php echo $cases['description']; ?></td>
                        <td>
                            <?php if ($cases['caseStatus'] == 0) { ?>
                                <i class="bi bi-question-circle text-warning"></i> Not Verified
                            <?php } else if($cases['caseStatus'] == 1){?>
                                <i class="bi bi-check-circle text-success"></i> Verified
                            <?php } else { ?>
                                <i class="bi bi-x-circle text-danger"></i> Rejected  
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($cases['caseStatus'] == 1) { ?>
                                <button class="btn btn-sm" disabled>Reviewed</button>
                            <?php } else { ?>
                                <form method="post" action="process_cancel.php">
                                    <input type="hidden" name="caseID" value="<?php echo $cases['caseID']; ?>">
                                    <button type="submit" name="cancel_death" class="btn btn-danger">Cancel</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Suspected Pets Tab -->
<div class="tab-pane <?=($active_tab == 4) ? ' active show' : '' ?> fade" id="suspectedPets">
    <!-- Suspected Pets Content -->
    <div class="container">
        <div class="container mt-4">
            <h2>Report Suspected Case</h2>
            <form method="POST" action="reportRabidBao.php">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                    <button type="submit" class="btn btn-primary">Report Death Case</button>
            </form>
        </div>
    </div>

    <div class="container mt-4">
        <h4>Suspected Rabid Dog Reports:</h4>
        <label for="residentSearch" class="form-label"></label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="residentSearch" placeholder="Search by name or email">
            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Pet's Name</th>
                    <th>Date Noticed</th>
                    <th>Signs</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="valid-s">
                <?php foreach ($suspected as $cases) { ?>
                    <tr>
                        <td><?php echo $cases['pname']; ?></td>
                        <td>
                            <?php  
                            $input_date = $cases['date'];
                            $date_obj = new DateTime($input_date);
                            $formatted_date = $date_obj->format("F j, Y, H:i:s");
                            echo $formatted_date;
                            ?>
                        </td>
                        <td><?php echo $cases['description']; ?></td>
                        <td>
                            <?php if ($cases['caseStatus'] == 0) { ?>
                                <i class="bi bi-question-circle text-warning"></i> Not Verified
                            <?php } else if($cases['caseStatus'] == 1){?>
                                <i class="bi bi-check-circle text-success"></i> Verified
                            <?php } else { ?>
                                <i class="bi bi-x-circle text-danger"></i> Rejected  
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($cases['caseStatus'] == 1) { ?>
                                <button class="btn btn-sm" disabled>Reviewed</button>
                            <?php } else { ?>
                                <form method="post" action="process_cancel.php">
                                    <input type="hidden" name="caseID" value="<?php echo $cases['caseID']; ?>">
                                    <button type="submit" name="cancel_sus" class="btn btn-danger">Cancel</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

    </div>
                
    <script>
        $(document).ready(function() {
            // Add Pet button click event
            $('#addPetButton').click(function() {
                // Submit the form using AJAX
                $.ajax({
                    url: 'process_addPet.php',
                    type: 'POST',
                    data: $('#addPetForm').serialize(),
                    success: function(response) {
                        // Handle the response
                        if (response.success) {
                            // Refresh the pet list
                            location.reload();
                        // } else {
                        //     // Display an error message
                        //     alert('Failed to add pet: ' + response.message);
                         }
                    },
                    error: function() {
                        // Display an error message
                        alert('An error occurred while processing the request.');
                    }
                });
            });
        });
    </script>
</body>

<?php
// Replace these with your actual database credentials
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'petstatvan';

// Create a mysqli connection
$mysqli = new mysqli($host, $user, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get the brgyID from the button
$brgyID = isset($_POST['brgyID']) ? $_POST['brgyID'] : null;

// Check if brgyID is valid before querying the database
if (!empty($brgyID)) {
    // Replace this with your actual query to fetch notifications from the database using brgyID
    $sql = "SELECT notifMessage, notifDate FROM notification WHERE brgyID = ? ORDER BY notifDate DESC";

    // Prepare the statement
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch notifications as an associative array
    $allNotifs = [];
    while ($row = $result->fetch_assoc()) {
        $allNotifs[] = $row;
    }

    // Close the statement
    $stmt->close();
} else {
    // If brgyID is not valid, set an empty array for notifications
    $allNotifs = [];
}

// Close the database connection
$mysqli->close();
?>

<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($allNotifs)) { ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allNotifs as $notif) { ?>
                                <tr>
                                    <td><?php echo $notif['notifMessage']; ?></td>
                                    <td><?php echo date('F j, Y, g:i A', strtotime($notif['notifDate'])); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p>No notifications available.</p>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



</html>
