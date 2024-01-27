<?php
session_start();

$active_tab = $_GET['active-tab'];

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
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
$pet = new Pet();
$cases = new Cases();

// Get all pets belonging to the user
$pets = $pet->getPetsByResidentID($user['residentID']);
$bites = $cases->getBitesByResidentID($user['residentID']);
$death = $cases->getDeathByResidentID($user['residentID']);
$suspected = $cases->getSuspectedCase($user['residentID']);
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
    ::placeholder {
        font-style: italic;
    }
    #petType{
        font-style: italic;
    }
    #sex{
        font-style: italic;
    }
    #neutering{
        font-style: italic;
    }
    #statusVac{
        font-style: italic;
    }
    #currentVac{
        font-style: italic;
    }
    #age{
        font-style: italic;
    }
    #pdescription{
        font-style: italic;
    }


</style>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Pet Dashboard</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form method="post" action="logout.php">
                        <button class="nav-link btn btn-primary">Logout</button>
                    </form>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="modal" data-bs-target="#notificationModal">
                        Notifications
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Welcome, <?php echo isset($user['name']) ? $user['name'] : ''; ?>!</h1>
        <p>Email: <?php echo isset($user['email']) ? $user['email'] : ''; ?></p>
    </div>

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
    <div class="tab-pane <?=($active_tab == 1) ? ' active show' : '' ?>" id="addPets">
        <div class="container mt-4">
            <h2>Add Pet</h2>
            <form method="POST" action="addPetRes.php">
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
                    if (empty($pets)) {
                        echo '<tr><td colspan="11">No data found.</td></tr>';
                    } else {
                        foreach ($pets as $pet) { 
                    ?>
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#petModal_<?php echo $pet['petID']; ?>">
                                    <?php echo $pet['pname']; ?>
                                </button>
                            </td>
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
                            <td class="d-flex justify-content-start">
                                <div class="d-flex flex-column align-items-center">
                                    <?php  
                                    $input_date = new DateTime($pet['currentVac']);
                                    echo $input_date->format("F j, Y");
                                    ?>
                                </div>
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
                                    <a href="#edit" data-bs-toggle="modal" style="display: inline-block;" class="editPet" data-petid="<?php echo $pet['petID'] ?>">
                                        <button type='button' class='btn btn-warning btn-sm'>
                                            <input type="hidden" name="petID" value="<?php echo $pet['petID']  ?>">
                                            <i class="bi bi-pencil-fill editPet"></i> Edit
                                        </button>
                                    </a>
                                    <form method="post" action="process_cancel.php" style="display: inline-block; margin-left: 5px;">
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
                    <?php 
                        } // End of foreach loop // End of if-else condition
                    ?>
                        

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
                    <form method="post" action="process_updateVacRes.php">
                        <!-- <input type="hidden" name="userType" value="<?php echo $user['userType']; ?>"> -->
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
        // Update modal input fields when the Report button is clicked
        $('.editPet').on('click', function () {
            var petID = $(this).data('petid');

            // Update the hidden input field for petID in the modal
            $('#petID').val(petID);

            // Update other hidden input fields if needed
            // $('#otherHiddenInput').val(someValue);

            // You can include more hidden input fields if necessary

            // Trigger the modal to open
            $('#edit').modal('show');
        });
    </script>
 
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
                            <label for="petName" class="form-label">Pet's Name:</label>
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
        
    <div class="tab-pane <?=($active_tab == 2) ? ' active show' : '' ?>" id="bitePets">
        <div class="container">
            <div class="container mt-4">
                <h2>Report Bite Case</h2>
                <form method="POST" action="addBiteCaseIndiv.php">
                    <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
                    <input type="hidden" name="residentID" value="<?php echo $residentID; ?>">
                    <input type="hidden" name="userType" id="userType" value="<?php echo $userType; ?>">
                    <button type="submit" class="btn btn-primary">Report Bite Case</button>
                </form>
            </div>
        </div>

        <div class="container mt-4">
            <h4>Bite Reports:</h4>
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
                <?php if (empty($bites)) { ?>
                    <tr>
                        <td colspan="6">No data</td>
                    </tr>
                <?php } else { ?>
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
                                    <!-- <a href="#editBite" data-bs-toggle="modal" style="display: inline-block;" class="editBite" data-caseid="<?php echo $cases['caseID']; ?>">
                                        <button type='button' class='btn btn-warning btn-sm'>
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </button>
                                    </a> -->
                                    <form method="post" action="process_cancel.php" style="display: inline-block; margin-left: 5px;">
                                        <input type="hidden" name="caseID" value="<?php echo $cases['caseID']; ?>">
                                        <button type="submit" name="cancel_bite" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x"></i> <!-- X icon for Delete -->
                                        </button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="tab-pane <?=($active_tab == 3) ? ' active show' : '' ?>" id="deathPets">
        <div class="container mt-4">
        <h2>Report Death Case</h2>
        <form method="POST" action="addDeathCaseIndiv.php">
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
                                <!-- <a href="#edit" data-bs-toggle="modal" style="display: inline-block;">
                                        <button type='button' class='btn btn-warning btn-sm'>
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </button>
                                    </a> -->
                                    <form method="post" action="process_cancel.php" style="display: inline-block; margin-left: 5px;">
                                            <input type="hidden" name="caseID" value="<?php echo $cases['caseID']; ?>">
                                            <button type="submit" name="cancel_death" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x"></i> <!-- X icon for Delete -->
                                            </button>
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
<div class="tab-pane <?=($active_tab == 4) ? ' active show' : '' ?>" id="suspectedPets">
    <!-- Suspected Pets Content -->
    <div class="container">
        <div class="container mt-4">
            <h2>Report Suspected Case</h2>
            <form method="POST" action="reportRabidResident.php">
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
                                <!-- <a href="#edit" data-bs-toggle="modal" style="display: inline-block;">
                                        <button type='button' class='btn btn-warning btn-sm'>
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </button>
                                    </a> -->
                                    <form method="post" action="process_cancel.php" style="display: inline-block; margin-left: 5px;">
                                            <input type="hidden" name="caseID" value="<?php echo $cases['caseID']; ?>">
                                            <button type="submit" name="cancel_sus" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x"></i> <!-- X icon for Delete -->
                                            </button>
                                        </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Function to enable/disable the "Add Pet" button based on vaccination status
        function updateAddPetButton() {
            var vaccinationStatus = $('#statusVac').val();
            var currentVacInput = $('#currentVacInput');

            // Disable the currentVacInput if the vaccination status is "1" (Wala)
            if (vaccinationStatus === "1") {
                currentVacInput.prop('disabled', true);
            } else {
                currentVacInput.prop('disabled', false);
            }
        }

        // Attach the function to the change event of the vaccination status field
        $('#statusVac').on('change', function () {
            updateAddPetButton();
        });

        // Initially, update the button state on page load
        updateAddPetButton();
    });
</script>

</body>
<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</html>
