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
$results = null;
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
    1 => "Unvaccinated"
);

$petType = array(
    0 => "Dog",
    1 => "Cat"
);

$confirmedRabies = array(
    0 => "Due to Rabies",
    1 => "Natural Causes",
);

$rabies = array(
    0 => "Rabies",
    1 => "Normal",
);
$bpartBitten = array(
    0 => "Head and Neck Area",
    1 => "Thorax Area",
    2 => "Abdomen Area",
    3 => "Upper Extremities",
    4 => "Lower Extremities",
);


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['selectedBarangay'])) {
        $selectedBarangay = $_POST['selectedBarangay'];
        $brgyID = $barangay->getBrgyID($selectedBarangay);
        $pets = $pet->getRegistries($brgyID);
        $bites = $case->getAllValidBiteCaseByBrgy($brgyID);
        $results = $case->getDeathByBrgy($brgyID);
        $rabid = $case->getRabidByBrgy($brgyID);
    } 
}
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
    <div class="container">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item" style="justify-content: start;">
                <a href="dashboardMAO.php" class="btn btn-primary">Back</a>
                </form>
                </li>
            </ul>
        </div>
    </nav>
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
            <label for="barangay" class="form-label">Barangay:</label>
            <select id="barangay" class="form-select" name="selectedBarangay" required>
                <option value="">Select Barangay</option>
                <?php
                $brgys = $barangay->getBrgys();
                foreach ($brgys as $brgy) {
                    echo '<option value="' . $brgy[0] . '">' . $brgy[2] . '</option>';
                }
                ?>
            </select> 
        </form>

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
                        <th>Latest Vaccination</th>
                        <th>Address</th>
                        <th>
                            <!-- <input type="text" class="form-control" id="registeredSearch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button> -->
                            <select class="form-control" id="yearSrch" style="width: 100px;" >
                                    <option value="">Year</option>
                                <?php
                                for ($year = 2010; $year <= 2050; $year++) {
                                    echo '<option value="' . $year . '">' . $year . '</option>';
                                }
                                ?>
                            </select>
                        </th>   
                        <th>
                        <button class="btn btn-primary" id="yearSrchBtn" type="button">Select</button>
                        </th>
                        </tr>
                       
                    </thead>
                    <tbody id="valid-r">
                        <?php
                       if ($pets) {
                            foreach ($pets as $pet) {
                                echo '<tr>';
                                echo '<td>' . $pet['name'] . '</td>';
                                $input_date = $row['regDate'];

                                // Convert the input date to a DateTime object
                                $date_obj = new DateTime($input_date);

                                // Format the date as "Month Day, Year"
                                $formatted_date = $date_obj->format("F j, Y");

                                // Print the formatted date
                                echo '<td>' . $formatted_date . '</td>'; 
                                echo '<td>' . $petType[$pet['petType']] . '</td>';
                                echo '<td>' . $pet['pname'] . '</td>';
                                echo '<td>' . $sex[$pet['sex']] . '</td>';
                                echo '<td>' . $pet['age'] . '</td>';
                                echo '<td>' . $neutering[$pet['Neutering']] . '</td>';
                                echo '<td>' . $pet['color'] . '</td>';
                                echo '<td>' . $vacStatus[$pet['statusVac']] . '</td>';
                                $input_date1 = $row['lastVaccination'];

                                // Convert the input date to a DateTime object
                                $date_obj1 = new DateTime($input_date1);

                                // Format the date as "Month Day, Year"
                                $formatted_date1 = $date_obj1->format("F j, Y");

                                // Print the formatted date
                                echo '<td>' . $formatted_date1 . '</td>'; 
                                $input_date2 = $row['currentVac'];

                                // Convert the input date to a DateTime object
                                $date_obj2 = new DateTime($input_date2);

                                // Format the date as "Month Day, Year"
                                $formatted_date2 = $date_obj2->format("F j, Y");

                                // Print the formatted date
                                echo '<td>' . $formatted_date2 . '</td>'; 
                                echo '<td>' . $pet['barangay'] . '</td>';    
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="10">No pet registries found for the selected barangay.</td></tr>';
                        }
                            ?>
                    </tbody>
                    <td><button id="registriesExp" onclick="RegistriesExport()">Export</button></td>
                </table>
            </div>
        </div>
                  <!-- Officers -->
    <div class="tab-content">
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
                            <select class="form-control" id="biteYrSrch" style="width: 100px;" >
                                    <option value="">Year</option>
                                <?php
                                for ($year = 2010; $year <= 2050; $year++) {
                                    echo '<option value="' . $year . '">' . $year . '</option>';
                                }
                                ?>
                            </select>
                        </th>   
                        <th>
                        <button class="btn btn-primary" id="biteYrSrchBtn" type="button">Select</button>
                        </th>
                    </tr>
                </thead>
                <tbody id="valid-c">
                    <?php
                    if ($cases) {
                        foreach ($bites as $cases) {
                            echo '<tr>';
                            echo '<td>' . $cases['victimsName'] . '</td>';
                            echo '<td>' . $petType[$cases['petType']] . '</td>';
                            echo '<td>' . $cases['pname'] . '</td>';
                            echo '<td>' . $cases['name'] . '</td>';
                            $input_date = $cases['date'];

                            // Convert the input date to a DateTime object
                            $date_obj = new DateTime($input_date);

                            // Format the date as "Month Day, Year"
                            $formatted_date = $date_obj->format("F j, Y, H:i:s");
                            echo '<td>' . $cases['barangay'] . '</td>';
                            echo '<td>' . $vacStatus[$pet['statusVac']] . '</td>';
                            echo '<td>' . $bpartBitten[$cases['bpartBitten']] . '</td>';
                            echo '<td>' . $cases['description'] . '</td>';
                            echo '<td>' . $rabies[$cases['confirmedRabies']] . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No pet bites found for the selected barangay.</td></tr>';
                    }
                    ?>
                    </tbody>
                    <td><button id="bitesExp" onclick="BitesExport()">Export</button></td>
                </table>
            </div>
        </div>

        <div class="tab-content">
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
                        <th>Address</th>
                        <th>Date Occurred</th>
                        <th>Description</th>            
                        <th>Rabies</th>
                        <th>
                            <!-- <input type="text" class="form-control" id="registeredSearch" placeholder="Search by name or email">
                            <button class="btn btn-primary" id="residentSearchBtn" type="button">Search</button> -->
                            <select class="form-control" id="deathYrSrch" style="width: 100px;" >
                                    <option value="">Year</option>
                                <?php
                                for ($year = 2010; $year <= 2050; $year++) {
                                    echo '<option value="' . $year . '">' . $year . '</option>';
                                }
                                ?>
                            </select>
                        </th>   
                        <th>
                        <button class="btn btn-primary" id="deathYrSrchBtn" type="button">Select</button>
                        </th>
                        </tr>
                    </tr>
                </thead>
                <tbody id="valid-d">
                    <?php
                 if ($results) {
                    foreach ($results as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td>' . $row['pname'] . '</td>';
                        echo '<td>' . $row['barangay'] . '</td>';
                        echo '<td>' . $row['date'] . '</td>';
                        echo '<td>' . $row['confirmedRabies'] . '</td>';
                        echo '<td>' . $row['description'] . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">No records found.</td></tr>';
                }
                    ?>
                    </tbody>
                    <td><button id="deathEXP" onclick="DeathExport()">Export</button></td>
                </table>
            </div>
        </div>
        
    <div class="tab-content">
        <div class="tab-pane fade" id="Rabid">
            <label for="rabidSrch" class="form-label"></label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="rabidSrch" placeholder="Search by name or email">
                <button class="btn btn-primary" id="rabidSrchBtn" type="button">Search</button>
            </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Owner's Name</th>
                            <th>Pet's Name</th>
                            <th>Address</th>
                            <th>Date Discovered</th>
                            <th>Description</th>
                            <th>Rabies</th>
                            <th>
                                <select class="form-control" id="rabidYrSrch" style="width: 100px;">
                                    <option value="">Year</option>
                                    <?php
                                    for ($year = 2010; $year <= 2050; $year++) {
                                        echo '<option value="' . $year . '">' . $year . '</option>';
                                    }
                                    ?>
                                </select>
                            </th>
                            <th>
                                <button class="btn btn-primary" id="rabidYrSrchBtn" type="button">Select</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="valid-s">
                        <?php
                        if ($cases) {
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
                                echo '<td>' . $rabids['barangay'] . '</td>';
                                echo '<td>' . $formatted_date . '</td>';
                                echo '<td>' . $rabids['description'] . '</td>';
                                echo '<td>' . ($rabids['confirmedRabies'] == 0 ? 'No' : 'Yes') . '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="3">No rabid reports found for the selected barangay.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <button id="rabidEXP" onclick="RabidsExport()">Export</button>
            </div>
            </div>

    
        <script>
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

            $("#rabidSrchBtn").click(function () {
                var searchValue = $("#rabidSrch").val().toLowerCase();
                var rows = $("#valid-s tr");

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
        </script>
          <script>
            $("#barangay").on("change", function () {
                $.post('ajax/getRegistriesByBrgy.php', {brgyid: $(this).val()}).done(function (data) {
                    $("#valid-r").html(data);
                });
            });

            $("#barangay").on("change", function () {
                $.post('ajax/getBitesByBrgy.php', {brgyid: $(this).val()}).done(function (data) {
                    $("#valid-c").html(data);
                });
            });

            $("#barangay").on("change", function () {
                $.post('ajax/getDeathByBrgy.php', {brgyid: $(this).val()}).done(function (data) {
                    $("#valid-d").html(data);
                });
            });
            $("#barangay").on("change", function () {
                $.post('ajax/getSusRabid.php', {brgyid: $(this).val()}).done(function (data) {
                    $("#valid-s").html(data);
                });
            });
        </script>
    </div>
    </body>
    </html>
   


