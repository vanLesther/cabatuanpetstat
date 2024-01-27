<?php
include_once "../class/cases.php";
include_once "../class/pet.php";
include_once "../class/resident.php";
include_once "../class/barangay.php";
$sex = array(
    0 => "Natural Cause",
    1 => "Due to Rabies"
);
$case = new Cases();
$cases = null;
if (isset($_POST['brgyid'])) {
    $brgyID = $_POST['brgyid'];
    
    $deaths = $case->getDeathByBrgy($brgyID);

    if ($case) {
        foreach ($deaths as $cases) {
            echo '<tr>';
            echo '<td>' . $cases['name'] . '</td>';
            echo '<td>' . $cases['pname'] . '</td>';
            echo '<td>' . $cases['barangay'] . '</td>';
            echo '<td>' . $cases['date'] . '</td>';
            echo '<td>' . $cases['description'] . '</td>';
            echo '<td>' . ($cases['confirmedRabies'] == 0 ? 'Natural Cause' : ' Rabies') . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No pet bites found for the selected barangay.</td></tr>';
    }
}
?>
 <script>  
       function DeathExport() {
    // Your data to be converted to CSV
            const data = [
                ['Owner\'s Name', 'Pet\'s Name', 'Barangay', 'Date', 'Description', '   Rabies', ''],
                <?php
                foreach ($deaths as $cases) {
                    $input_date = $cases['date'];
                    $date_obj = new DateTime($input_date);
                    $formatted_date = $date_obj->format("F j-Y");

                    echo '["' . $cases['name'] . '","' . $cases['pname'] . '","' . $cases['barangay'] . '","' . $formatted_date . '","' . $cases['description'] . '","' . ($cases['confirmedRabies'] == 0 ? 'Natural Cause' : ' Rabies') . '"],';
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

            // Remove the link element
            document.body.removeChild(link);
        }

                        $("#deathYrSrchBtn").click(function () {
                            var searchValue = $("#deathYrSrch").val().toLowerCase();
                            var rows = $("#valid-d tr");

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
                                    $("#valid-d").append('<tr><td colspan="11">No matching results found.</td></tr>');
                                }
                            }
                        });

                    </script>