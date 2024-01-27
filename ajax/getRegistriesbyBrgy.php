<?php
include_once "../class/pet.php";
include_once "../class/barangay.php";
include_once "../class/resident.php";
include_once "../class/barangay.php";

$pet = new Pet();
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

if (isset($_POST['brgyid'])) {
    $brgyID = $_POST['brgyid'];
    
    $pets = $pet->getRegistries($brgyID);

                if ($pets) {

                    foreach ($pets as $pet) {
                        echo '<tr>';
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
                    }
                    
?>
                    <script>  
                        function RegistriesExport() {
                            var searchValue = $("#yearSrch").val().toLowerCase();
                            var rows = $("#valid-r tr:visible");    
                            // Your data to be converted to CSV
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

                            // Remove the link element
                            document.body.removeChild(link);
                        }

                        $("#yearSrchBtn").click(function () {
                            var searchValue = $("#yearSrch").val().toLowerCase();
                            var rows = $("#valid-r tr");

                            rows.show(); // Show all rows

                            if (searchValue.trim() !== "") {
                                // Filter rows based on the regDate column
                                var matchingRows = rows.filter(function () {
                                    return $(this).find('td:eq(10)').text().toLowerCase().indexOf(searchValue) > -1;
                                });

                                if (matchingRows.length > 0) {
                                    rows.hide(); // Hide all rows
                                    matchingRows.show(); // Show matching rows
                                } else {
                                    // Hide all rows and display "No matching results found" message
                                    rows.hide();
                                    $("#valid-r").append('<tr><td colspan="11">No matching results found.</td></tr>');
                                }
                            }
                        });

                    </script>
