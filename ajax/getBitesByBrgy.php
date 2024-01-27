<?php
include_once "../class/cases.php";
include_once "../class/pet.php";
include_once "../class/resident.php";
include_once "../class/barangay.php";

$case = new Cases();
$rabies = array(
    0 => "No",
    1 => "Yes"
);
$bpartBitten = array(
    0 => "Head and Neck Area",
    1 => "Thorax Area",
    2 => "Abdomen Area",
    3 => "Upper Extremities",
    4 => "Lower Extremities",
);
$petType = array(
    0 => "Dog",
    1 => "Cat",
);
$vacStatus = array(
    0 => "Vaccinated",
    1 => "Unvaccinated",
);
if (isset($_POST['brgyid'])) {
    $brgyID = $_POST['brgyid'];
    $cases = new Cases();
    
    $bites = $case->getAllValidBiteCaseByBrgy($brgyID);

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
            echo '<td>' . $formatted_date = $date_obj->format("F j, Y, H:i:s") . '</td>';
            echo '<td>' . $cases['barangay'] . '</td>';
            echo '<td>' . $vacStatus[$cases['statusVac']] . '</td>';
            echo '<td>' . $bpartBitten[$cases['bpartBitten']] . '</td>';
            echo '<td>' . $cases['description'] . '</td>';
            echo '<td>' . $rabies[$cases['confirmedRabies']] . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No pet bites found for the selected barangay.</td></tr>';
    }
}
?>
<script>  
           function BitesExport() {
                    // Prevent the default action (form submission or link click)
                    event.preventDefault();

                    // Your data to be converted to CSV
                    const data = [
                        ['Victim\'s Name', 'Pet Type', 'Pet Name', 'Owner\'s Name', 'Date', 'Barangay', 'Vaccination Status', 'Bitten Area', 'Description', 'Confirmed Rabies'],
                        <?php
                        foreach ($bites as $case) {
                            // Convert the input date to a DateTime object
                            $input_date = $case['date'];
                            $date_obj = new DateTime($input_date);
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


                        $("#biteYrSrchBtn").click(function () {
                            var searchValue = $("#biteYrSrch").val().toLowerCase();
                            var rows = $("#valid-c tr");

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
                                    $("#valid-c").append('<tr><td colspan="11">No matching results found.</td></tr>');
                                }
                            }
                        });

                    </script>