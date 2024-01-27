<?php
require_once("class/pet.php");
require_once("class/barangay.php");
require_once("class/db_connect.php");
require_once("class/resident.php");
require_once("class/cases.php");

$pet = new Pet();
$barangay = new Barangay();
$resident = new Resident();
$case = new Cases();

$cases = null;
$officers = null;
$pets = null;

$sex = array(
    0 => "Male",
    1 => "Female"
);

$neutering = array(
    0 => "Not Neutered",
    1 => "Neutered"
);

$vacStatus = array(
    0 => "Vaccinated",
    1 => "Not Vaccinated"
);

$petType = array(
    0 => "Dog",
    1 => "Cat"
);

$brgyID = isset($_POST["brgyID"]) ? $_POST["brgyID"] : null;

$pets = $pet->getRegistries($brgyID);
$bites = $case->getAllValidBiteCaseByBrgy($brgyID);
$death = $case->getDeathByBrgy($brgyID);
$rabid = $case->getRabidByBrgy($brgyID);
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
        <form method="POST" action="./dashboard1.php?active-tab=1" id="genReg">
            <input type="hidden" name="brgyID" value="<?php echo $brgyID; ?>">
            <input type="submit" value="Back" class="btn btn-primary">
        </form>
        </div>
        <h1>Tabular Form</h1>
        <form method="POST" action="" id="tabform" name="Tabform">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#registered" data-bs-toggle="tab">Registries</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#Cases" data-bs-toggle="tab">Bites</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#Deaths" data-bs-toggle="tab">Deaths</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#Rabid" data-bs-toggle="tab">Rabid</a>
                </li>
            </ul>
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Valid Residents -->
                <div class="tab-pane fade show active" id="registered">
                    <table class="table">
                    <label for="regSrch" class="form-label"></label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="regSrch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="regSrchBtn" type="button">Search</button>
                        </div>
                        <thead>
                            <tr>
                                <th>Owner's Name</th>
                                <th>Date of Registry</th>
                                <th>Pet Type</th>
                                <th>Name of Pet</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Neutering</th>
                                <th>Color</th>
                                <th>Vaccination Status</th>
                                <th>Last Vaccination</th>
                                <th>Current Vaccination</th>
                                <th>Address</th>
                                <th>
                            <!-- <input type="text" class="form-control" id="registeredSearch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button> -->
                            <select class="form-control" id="RegYrs" style="width: 100px;" >
                                    <option value="">Year</option>
                                <?php
                                for ($year = 2010; $year <= 2050; $year++) {
                                    echo '<option value="' . $year . '">' . $year . '</option>';
                                }
                                ?>
                            </select>
                        </th>   
                        <th>
                        <button class="btn btn-primary" id="RegYrsBtn" type="button">Select</button>
                        </th>
                            </tr>
                        </thead>
                        <tbody id="valid-r">
                            <?php
                            if ($pets) {
                                foreach ($pets as $pet) {
                                    echo '<tr>';
                                    echo '<td>' . $pet['name'] . '</td>';
                                    $input_date1 = $pet['regDate'];

                                    // Convert the input date to a DateTime object
                                    $date_obj = new DateTime($input_date1);

                                    // Format the date as "Month Day, Year"
                                    $formatted_date1 = $date_obj->format("F j, Y");

                                    // Print the formatted date
                                    echo '<td>' . $formatted_date1 . '</td>'; 
                                    echo '<td>' . $petType[$pet['petType']] . '</td>';
                                    echo '<td>' . $pet['pname'] . '</td>';
                                    echo '<td>' . $sex[$pet['sex']] . '</td>';
                                    echo '<td>' . $pet['age'] . '</td>';
                                    echo '<td>' . $neutering[$pet['Neutering']] . '</td>';
                                    echo '<td>' . $pet['color'] . '</td>';
                                    echo '<td>' . $vacStatus[$pet['statusVac']] . '</td>';
                                    $input_date2 = $pet['lastVaccination'];

                                    // Convert the input date to a DateTime object
                                    $date_obj = new DateTime($input_date2);

                                    // Format the date as "Month Day, Year"
                                    $formatted_date2 = $date_obj->format("F j, Y");

                                    // Print the formatted date
                                    echo '<td>' . $formatted_date2 . '</td>'; 
                                    
                                    $input_date = $pet['currentVac'];

                                    // Convert the input date to a DateTime object
                                    $date_obj = new DateTime($input_date);

                                    // Format the date as "Month Day, Year"
                                    $formatted_date = $date_obj->format("F j, Y");

                                    // Print the formatted date
                                    echo '<td>' . $formatted_date . '</td>'; 
                                    echo '<td>' . $pet['barangay'] . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="11">No pet registries found for the selected barangay.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <td><button id="registriesExp" onclick="RegistriesExport()">Export</button></td>
                </div>

                <!-- Cases -->
                <div class="tab-pane fade" id="Cases">
                    <table class="table">
                    <label for="casesSearch" class="form-label"></label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="casesSearch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="casesSrchBtn" type="button">Search</button>
                        </div>
                        <thead>
                            <tr>
                                <th>Victim</th>
                                <th>Species</th>
                                <th>Pet's Name</th>
                                <th>Owner's Name</th>
                                <th>Date Occurred</th>
                                <th>Address</th>
                                <th>Vaccination Status</th>
                                <th>Body Part Bitten</th>
                                <th>Description</th>
                                <th>Rabies</th>
                                <th>
                            <!-- <input type="text" class="form-control" id="registeredSearch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button> -->
                            <select class="form-control" id="BiteYrs" style="width: 100px;" >
                                    <option value="">Year</option>
                                <?php
                                for ($year = 2010; $year <= 2050; $year++) {
                                    echo '<option value="' . $year . '">' . $year . '</option>';
                                }
                                ?>
                            </select>
                        </th>   
                        <th>
                        <button class="btn btn-primary" id="BiteYrsBtn" type="button">Select</button>
                        </th>
                        </tr>
                            </tr>
                        </thead>
                        <tbody id="valid-c">
                            <?php
                            if ($bites) {
                                foreach ($bites as $case) {
                                    echo '<tr>';
                                    echo '<td>' . $case['victimsName'] . '</td>';
                                    echo '<td>' . ($case['petType'] == 0 ? 'Dog' : 'Cat') . '</td>';
                                    echo '<td>' . $case['pname'] . '</td>';
                                    echo '<td>' . $case['name'] . '</td>';
                                    $input_date = $case['date'];

                                    // Convert the input date to a DateTime object
                                    $date_obj = new DateTime($input_date);

                                    // Format the date as "Month Day, Year"
                                    $formatted_date = $date_obj->format("F j, Y");

                                    // Print the formatted date
                                    echo '<td>' . $formatted_date . '</td>'; 
                                    echo '<td>' . $case['barangay'] . '</td>';
                                    echo '<td>' . ($case['statusVac'] == 0 ? 'Vaccinated' : 'Unvaccinated') . '</td>';
                                    echo '<td>' . (
                                        $case['bpartBitten'] == 0 ? 'Head and Neck Area' :
                                        ($case['bpartBitten'] == 1 ? 'Thorax Area' :
                                        ($case['bpartBitten'] == 2 ? 'Abdomen Area' :
                                        ($case['bpartBitten'] == 3 ? 'Upper Extremities' :
                                        ($case['bpartBitten'] == 4 ? 'Lower Extremities' : 'Unknown')))))
                                        . '</td>';
                                    echo '<td>' . $case['description'] . '</td>';
                                    echo '<td>' . ($case['confirmedRabies'] == 0 ? 'No' : 'Yes'). '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7">No pet bites found for the selected barangay.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <button id="BitesEXP" onclick="BitesExport()">Export</button>
                </div>

                <!-- Death -->
                <div class="tab-pane fade" id="Deaths">
                    <table class="table">
                    <label for="deathSearch" class="form-label"></label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="deathSearch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="DeathSrchBtn" type="button">Search</button>
                        </div>
                        <thead>
                            <tr>
                                <th>Owner's Name</th>
                                <th>Pet's Name</th>
                                <th>Date Occured</th>
                                <th>Description</th>
                                <th>Address</th>
                                <th>Rabies</th>
                                <th>
                            <!-- <input type="text" class="form-control" id="registeredSearch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button> -->
                            <select class="form-control" id="DeathYrs" style="width: 100px;" >
                                    <option value="">Year</option>
                                <?php
                                for ($year = 2010; $year <= 2050; $year++) {
                                    echo '<option value="' . $year . '">' . $year . '</option>';
                                }
                                ?>
                            </select>
                        </th>   
                        <th>
                        <button class="btn btn-primary" id="DeathYrsBtn" type="button">Select</button>
                        </th>
                        </tr>
                            </tr>
                        </thead>
                        <tbody id="valid-d">
                            <?php
                            if ($death) {
                                foreach ($death as $deaths) {
                                    echo '<tr>';
                                    echo '<td>' . $deaths['name'] . '</td>';
                                    echo '<td>' . $deaths['pname'] . '</td>';
                                    $input_date = $deaths['date'];

                                    // Convert the input date to a DateTime object
                                    $date_obj = new DateTime($input_date);

                                    // Format the date as "Month Day, Year"
                                    $formatted_date = $date_obj->format("F j, Y");

                                    // Print the formatted date
                                    echo '<td>' . $formatted_date . '</td>'; 
                                    echo '<td>' . $deaths['description'] . '</td>';
                                    echo '<td>' . $deaths['barangay'] . '</td>';
                                    echo '<td>' . ($deaths['confirmedRabies'] == 0 ? 'Natural Cause' : 'Rabies') . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="3">No pet deaths found for the selected barangay.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <td><button id="deathEXP" onclick="DeathsExport()">Export</button></td>
                </div>
            </div>

            <div class="tab-pane fade" id="Rabid">
                    <table class="table">
                    <label for="rabidSrch" class="form-label"></label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="rabidSrch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="rabidSrchBtn" type="button">Search</button>
                        </div>
                        <thead>
                            <tr>
                                <th>Owner's Name</th>
                                <th>Pet's Name</th>
                                <th>Date Discovered</th>
                                <th>Description</th>
                                <th>Address</th>
                                <th>Rabies</th>
                                <th>
                            <!-- <input type="text" class="form-control" id="registeredSearch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button> -->
                            <select class="form-control" id="rabidYrs" style="width: 100px;" >
                                    <option value="">Year</option>
                                <?php
                                for ($year = 2010; $year <= 2050; $year++) {
                                    echo '<option value="' . $year . '">' . $year . '</option>';
                                }
                                ?>
                            </select>
                        </th>   
                        <th>
                        <button class="btn btn-primary" id="rabidYrsSrchBtn" type="button">Select</button>
                        </th>
                        </tr>
                            </tr>
                        </thead>
                        <tbody id="valid-s">
                            <?php
                            if ($rabid) {
                                foreach ($rabid as $rabids) {
                                    echo '<tr>';
                                    echo '<td>' . $rabids['name'] . '</td>';
                                    echo '<td>' . $rabids['pname'] . '</td>';
                                    $input_date = $rabids['date'];

                                    // Convert the input date to a DateTime object
                                    $date_obj = new DateTime($input_date);

                                    // Format the date as "Month Day, Year"
                                    $formatted_date = $date_obj->format("F j, Y");

                                    // Print the formatted date
                                    echo '<td>' . $formatted_date . '</td>'; 
                                    echo '<td>' . $rabids['description'] . '</td>';
                                    echo '<td>' . $rabids['barangay'] . '</td>';
                                    echo '<td>' . ($rabids['confirmedRabies'] == 0 ? 'No' : 'Yes') . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="3">No rabid reports found for the selected barangay.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <td><button id="rabidEXP" onclick="RabidsExport()">Export</button></td>
                </div>
            </div>
            <script>
                // Export buttons
        function RegistriesExport() {
                // Prevent the default action (form submission or link click)
                event.preventDefault();

                // var searchValue = $("#yearSrch").val().toLowerCase();
                // var rows = $("#valid-r tr:visible");    
                // // Your data to be converted to CSV
                const data = [
                    ['Owner\'s Name', 'Date of Registry', 'Pet Type', 'Name of Pet', 'Sex', 'Age', 'Neutering', 'Color', 'Vaccination Status', 'Last Vaccination', 'Current Vaccination', 'Address', ''],
                    <?php
                    foreach ($pets as $pet) {
                        $input_date1 = $pet['regDate'];
                        $date_obj1 = new DateTime($input_date1);
                        $formatted_date1 = $date_obj1->format("F j-Y");

                        $input_date2 = $pet['lastVaccination'];
                        $date_obj2 = new DateTime($input_date2);
                        $formatted_date2 = $date_obj2->format("F j-Y");

                        $input_date3 = $pet['currentVac'];
                        $date_obj3 = new DateTime($input_date3);
                        $formatted_date3 = $date_obj3->format("F j-Y");

                        echo '["' . $pet['name'] . '","' . $formatted_date1 . '","' . $petType[$pet['petType']] . '","' . $pet['pname'] . '","' . $sex[$pet['sex']] . '","' . $pet['age'] . '","' . $neutering[$pet['Neutering']] . '","' . $pet['color'] . '","' . $vacStatus[$pet['statusVac']] . '","' . $formatted_date2 . '","' . $formatted_date3 . '","' . $pet['barangay'] . '"],';
                    }
                    ?>
                ];

                // Convert the data to CSV format
                const csvContent = data.map(row => row.join(',')).join('\n');

                // Create a Blob containing the CSV data
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                // Create a link element to trigger the download
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'Registries.csv');
                document.body.appendChild(link);

                // Trigger the click event on the link to initiate the download
                link.click();

                // Remove the link element after a delay to ensure the download has started
                setTimeout(function() {
                    document.body.removeChild(link);
                }, 1000); // You can adjust the delay as needed
            }

            function BitesExport() {
                    // Prevent the default action (form submission or link click)
                    event.preventDefault();

                    // Your data to be converted to CSV
                    const data = [
                        ['Victim\'s Name', 'Pet Type', 'Pet Name', 'Owner\'s Name', 'Date', 'Barangay', 'Vaccination Status', 'Bitten Area', 'Description', 'Confirmed Rabies'],
                        <?php
                        foreach ($bites as $case) {
                            $input_date = $case['date'];
                            // Convert the input date to a DateTime object
                            $date_obj = new DateTime($case['date']);
                            // Format the date as "F j, Y"
                            $formatted_date = $date_obj->format("F j, Y");

                            echo '["' . $case['victimsName'] . '","' . ($case['petType'] == 0 ? 'Dog' : 'Cat') . '","' . $case['pname'] . '","' . $case['name'] . '","' . $formatted_date . '","' . $case['barangay'] . '","' . ($case['statusVac'] == 0 ? 'Vaccinated' : 'Unvaccinated') . '","' . (
                                $case['bpartBitten'] == 0 ? 'Head and Neck Area' :
                                ($case['bpartBitten'] == 1 ? 'Thorax Area' :
                                ($case['bpartBitten'] == 2 ? 'Abdomen Area' :
                                ($case['bpartBitten'] == 3 ? 'Upper Extremities' :
                                ($case['bpartBitten'] == 4 ? 'Lower Extremities' : 'Unknown'))))) . '","' . $case['description'] . '","' . ($case['confirmedRabies'] == 0 ? 'Natural Cause' : 'Rabies') . '"],';
                        }
                        ?>
                    ];

                    // Convert the data to CSV format
                    const csvContent = data.map(row => row.join(',')).join('\n');

                    // Create a Blob containing the CSV data
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                    // Create a link element to trigger the download
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.setAttribute('download', 'Cases.csv');
                    document.body.appendChild(link);

                    // Trigger the click event on the link to initiate the download
                    link.click();

                    // Remove the link element after a delay to ensure the download has started
                    setTimeout(function () {
                        document.body.removeChild(link);
                    }, 1000); // You can adjust the delay as needed
                }


            function DeathsExport() {
                // Prevent the default action (form submission or link click)
                event.preventDefault();

                // Your data to be converted to CSV
                const data = [
                    ['Owner\'s Name', 'Pet Name', 'Date', 'Description', 'Barangay', 'Rabies Status'],
                    <?php
                    foreach ($death as $deaths) {
                        $input_date = $deaths['date'];
                        $date_obj = new DateTime($input_date);
                        $formatted_date = $date_obj->format("F j Y");

                        echo '["' . $deaths['name'] . '","' . $deaths['pname'] . '","' . $formatted_date . '","' . $deaths['description'] . '","' . $deaths['barangay'] . '","' . ($deaths['confirmedRabies'] == 0 ? 'Natural Cause' : 'Rabies') . '"],';
                    }
                    ?>
                ];

                // Convert the data to CSV format
                const csvContent = data.map(row => row.join(',')).join('\n');

                // Create a Blob containing the CSV data
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                // Create a link element to trigger the download
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'Deaths.csv');
                document.body.appendChild(link);

                // Trigger the click event on the link to initiate the download
                link.click();

                // Remove the link element after a delay to ensure the download has started
                setTimeout(function() {
                    document.body.removeChild(link);
                }, 1000); // You can adjust the delay as needed
            }

                function RabidsExport() {
                    // Prevent the default action (form submission or link click)
                    event.preventDefault();

                    // Your data to be converted to CSV
                    const data = [
                        ['Owner\'s Name', 'Pet Name', 'Date', 'Description', 'Barangay', 'Rabies Status'],
                        <?php
                        foreach ($rabid as $rabids) {
                            $input_date = $rabids['date'];
                            $date_obj = new DateTime($input_date);
                            $formatted_date = $date_obj->format("F j Y");

                            echo '["' . $rabids['name'] . '","' . $rabids['pname'] . '","' . $formatted_date . '","' . $rabids['description'] . '","' . $rabids['barangay'] . '","' . ($rabids['confirmedRabies'] == 0 ? 'No' : 'Yes') . '"],';
                        }
                        ?>
                    ];

                    // Convert the data to CSV format
                    const csvContent = data.map(row => row.join(',')).join('\n');

                    // Create a Blob containing the CSV data
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                    // Create a link element to trigger the download
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.setAttribute('download', 'Rabids.csv');
                    document.body.appendChild(link);

                    // Trigger the click event on the link to initiate the download
                    link.click();

                    // Remove the link element after a delay to ensure the download has started
                    setTimeout(function () {
                        document.body.removeChild(link);
                    }, 1000); // You can adjust the delay as needed
                }


            </script>

            <script>
                // year search buttons
                //registries
                $("#RegYrsBtn").click(function () {
                var searchValue = $("#RegYrs").val().toLowerCase();
                var rows = $("#valid-r tr");

                var matchingRows = rows.filter(function () {
                    return $(this).text().toLowerCase().indexOf(searchValue) > -1;
                });

                rows.hide(); // Hide all rows

                if (matchingRows.length > 0) {
                    matchingRows.show(); // Show matching rows
                } else {
                    // Display a message when there are no search results
                    $("#valid-r").append('<tr><td colspan="6">No matching results found.</td></tr>');
                }
            });
            //bites
            $("#BiteYrsBtn").click(function () {
                var searchValue = $("#BiteYrs").val().toLowerCase();
                var rows = $("#valid-c tr");

                var matchingRows = rows.filter(function () {
                    return $(this).text().toLowerCase().indexOf(searchValue) > -1;
                });

                rows.hide(); // Hide all rows

                if (matchingRows.length > 0) {
                    matchingRows.show(); // Show matching rows
                } else {
                    // Display a message when there are no search results
                    $("#valid-c").append('<tr><td colspan="6">No matching results found.</td></tr>');
                }
            });

            // deaths
            $("#DeathYrsBtn").click(function () {
                var searchValue = $("#DeathYrs").val().toLowerCase();
                var rows = $("#valid-d tr");

                var matchingRows = rows.filter(function () {
                    return $(this).text().toLowerCase().indexOf(searchValue) > -1;
                });

                rows.hide(); // Hide all rows

                if (matchingRows.length > 0) {
                    matchingRows.show(); // Show matching rows
                } else {
                    // Display a message when there are no search results
                    $("#valid-d").append('<tr><td colspan="6">No matching results found.</td></tr>');
                }
            });
            $("#DeathYrsBtn").click(function () {
                var searchValue = $("#DeathYrs").val().toLowerCase();
                var rows = $("#valid-d tr");

                var matchingRows = rows.filter(function () {
                    return $(this).text().toLowerCase().indexOf(searchValue) > -1;
                });

                rows.hide(); // Hide all rows

                if (matchingRows.length > 0) {
                    matchingRows.show(); // Show matching rows
                } else {
                    // Display a message when there are no search results
                    $("#valid-d").append('<tr><td colspan="6">No matching results found.</td></tr>');
                }
            });

            </script>

            <script>
                // search buttons
                $("#regSrchBtn").click(function () {
                var searchValue = $("#regSrch").val().toLowerCase();
                var rows = $("#valid-r tr");

                if (searchValue.trim() === "") {
                    rows.show(); 
                } else {
                    rows.hide(); 

                    
                    var matchingRows = rows.filter(function () {
                        return $(this).text().toLowerCase().indexOf(searchValue) > -1;
                    });

                    if (matchingRows.length > 0) {
                        matchingRows.show();
                    } else {
                        $("#valid-r").append('<tr><td colspan="13">No matching results found.</td></tr>');
                    }
                }
            });

            $("#casesSrchBtn").click(function () {
                var searchValue = $("#casesSearch").val().toLowerCase();
                var rows = $("#valid-c tr");

                if (searchValue.trim() === "") {
                    rows.show(); 
                } else {
                    rows.hide(); 

                    
                    var matchingRows = rows.filter(function () {
                        return $(this).text().toLowerCase().indexOf(searchValue) > -1;
                    });

                    if (matchingRows.length > 0) {
                        matchingRows.show();
                    } else {
                        $("#valid-c").append('<tr><td colspan="13">No matching results found.</td></tr>');
                    }
                }
            });

            $("#DeathSrchBtn").click(function () {
                var searchValue = $("#deathSearch").val().toLowerCase();
                var rows = $("#valid-d tr");

                if (searchValue.trim() === "") {
                    rows.show(); 
                } else {
                    rows.hide(); 

                    
                    var matchingRows = rows.filter(function () {
                        return $(this).text().toLowerCase().indexOf(searchValue) > -1;
                    });

                    if (matchingRows.length > 0) {
                        matchingRows.show();
                    } else {
                        $("#valid-d").append('<tr><td colspan="13">No matching results found.</td></tr>');
                    }
                }
            });

            $("#rabidYrsSrchBtn").click(function () {
                var searchValue = $("#rabidYrs").val().toLowerCase();
                var rows = $("#valid-s tr");

                if (searchValue.trim() === "") {
                    rows.show();
                } else {
                    rows.hide();

                    var matchingRows = rows.filter(function () {
                        var rowData = $(this).text().toLowerCase();
                        return rowData.indexOf(searchValue) > -1;
                    });

                    if (matchingRows.length > 0) {
                        matchingRows.show();
                    } else {
                        $("#valid-s").append('<tr><td colspan="13">No matching results found.</td></tr>');
                    }
                }
            });


        </script>
        </form>
    </div>
</body>
</html>
