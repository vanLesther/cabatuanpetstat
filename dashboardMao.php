<?php
session_start();
require_once "class/db_connect.php";
require_once("class/barangay.php");
require_once("class/cases.php");

$barangay = new Barangay();
$case = new Cases();
$count = new Cases();
$count1 = new Cases();
$count2 = new Cases();
$count3 = new Cases();
$lat = 0;
$lng = 0;
$lat1 = 0;
$lng1 = 0;
if (isset($_POST['selectedBarangay'])) {
    $brgyID = $_POST['selectedBarangay'];

    // First Function
    $users = $case->getAllValidCaseByBarangay($brgyID);

    if ($users && $users->num_rows > 0) {
        $heatmapData = [];

        while ($row = $users->fetch_assoc()) {
            // Check if 'latitude' and 'longitude' keys exist in $row before accessing them
            if (isset($row['latitude'], $row['longitude'])) {
                $lat = $row['latitude'];
                $lng = $row['longitude'];
                $heatmapData[] = [$lat, $lng]; // Push data to the array
            }
        }
    }

    $brgy = $barangay->getBrgyLocation($brgyID);

    if ($brgy && isset($brgy[0]['latitude'], $brgy[0]['longitude'])) {
        $lat1 = $brgy[0]['latitude'];
        $lng1 = $brgy[0]['longitude'];
        $barangayName = $brgy[0]['barangay'];
    }

    $counts = $count->countCase($brgyID);

    $counts1 = $count1->countAllCase($brgyID);

    $counts2 = $count2->countAllCasePerYear($brgyID);

    $counts3 = $count3->countAllValidCasePerYear($brgyID);

    echo var_dump($counts, $counts2, $counts2, $counts3);
} else {
    // Handle the case when selectedBarangay is not set
    echo json_encode(['error' => 'Selected barangay not provided.']);
}

// Now you can use $lat, $lng, $lat1, and $lng1 outside of their respective scopes if needed.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vector Tiles in Leaflet JS</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v1.1.1/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v1.1.1/maptiler-sdk.css" rel="stylesheet" />
    <script src="https://cdn.maptiler.com/leaflet-maptilersdk/v1.0.0/leaflet-maptilersdk.js"></script>
    <script src="https://unpkg.com/heatmap.js"></script>
    <script src="https://unpkg.com/leaflet.heat"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
            color: #333; /* Change color to your preference */
            font-size: 24px; /* Change font size to your preference */
            font-weight: bold; /* Make it bold or adjust weight */
            text-transform: uppercase; /* Convert text to uppercase if desired */
        }

        #container {
            display: flex;
        }

        #sidebar {
            width: 250px;
            background-color: #333;
            color: #fff;
            padding: 20px;
            box-sizing: border-box;
        }

        #main-content {
            flex: 1;
            padding: 20px;
            box-sizing: border-box;
        }

        #map {
            height: 50vh;
            width: 50vw;
            margin: 10px auto;
            border: 2px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        #myChart, #pieChart {
            height: 50vh;
            width: 50vw;
            margin: 10px auto;
            border: 2px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div id="container">
        <div id="sidebar">
            <label for="barangay" class="form-label">Barangay:</label>
            <select id="barangay" class="form-select" name="selectedBarangay" required>
                <option value="">Select Barangay</option>
                <?php
                global $conn;
                $query = "SELECT brgyID, barangay FROM barangay";
                $result = mysqli_query($conn, $query);

                // Loop through the query results and generate options
                while ($row = mysqli_fetch_assoc($result)) {
                    $brgyID = $row['brgyID'];
                    $barangay = $row['barangay'];
                    echo "<option value='$brgyID'>$barangay</option>";
                }
                // Release the result set
                mysqli_free_result($result);

                // Check for errors
                if (!$result) {
                    die("Database query failed.");
                }
                ?>
            </select>
            <div class="mb-3">
            </div>
            <div class="mb-3">
                <form method="POST" action="assign_officer.php" id="assignForm">
                    <input type="submit" value="Assign Officer" class="btn btn-primary">
                </form>
                </div>
                <div class="mb-3">
                    <form method="POST" action="tabular.php" id="statisticsForm">
                    <input type="submit" value="View Reports" class="btn btn-primary">
                </form>
                </div>
                <div class="mb-3">
                        <form method="POST" action="logout.php" id="statisticsForm">
                    <input type="submit" value="Log Out" class="btn btn-primary">
                </form>
            </div>
        </div>
        <div id="main-content">
            <h1>Bite Case Report for <?php echo $barangayName ?></h1>
            <div id="map"></div>
            <script>
                const key = 'A8yOIIILOal2yE0Rvb63';
                
                // Use PHP to echo the latitude and longitude values
                const lat1 = <?php echo $lat1; ?>;
                const lng1 = <?php echo $lng1; ?>;
                
                const map = L.map('map').setView([lat1, lng1], 15);
                
                const mtLayer = L.maptilerLayer({
                    apiKey: key,
                    style: "8a85054c-5879-4e0b-b2f8-7f9564b6e3f8", //optional
                }).addTo(map);

                var heatmapData = [];

                // Create heatmap layer using Leaflet Heatmap Overlay plugin
                var heat = L.heatLayer(heatmapData, {
                    radius: 20,
                    blur: 30,
                    maxZoom: 18,
                    max: .3, // Remove concentrationFactor from here
                }).addTo(map);

                $(document).ready(function () {
                    // Listen for changes in the select element
                    $("#barangay").change(function () {
                        // Get the selected value
                        var selectedBarangay = $(this).val();

                        // Create a form dynamically
                        var form = $('<form action="dashboardMAO.php" method="POST">' +
                            '<input type="hidden" name="selectedBarangay" value="' + selectedBarangay + '">' +
                            '</form>');

                        // Append the form to the body and submit it
                        $('body').append(form);
                        form.submit();
                    });
                });

                // Update heatmap data and redraw the layer when needed
                function updateHeatmapData(newData) {
                    heatmapData = newData;
                    heat.setLatLngs(newData).redraw();
                }

                <?php
                // JavaScript block with PHP values
                if (isset($heatmapData)) {
                    echo "var initialData = " . json_encode($heatmapData) . ";";
                    echo "updateHeatmapData(initialData);"; // Update the heatmap initially with fetched data
                }
                ?>
            </script>

            <canvas id="myChart"></canvas>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Data for the chart
                    <?php
                    // JavaScript block with PHP values for the chart
                    if (isset($counts, $counts1)) {
                        // Create an array with all month names
                        $allMonths = [
                            'January', 'February', 'March', 'April', 'May', 'June',
                            'July', 'August', 'September', 'October', 'November', 'December'
                        ];

                        // Initialize labels and data arrays
                        $labels = [];
                        $data = [];
                        $data1 = [];


                        // Loop through all months
                        foreach ($allMonths as $monthName) {
                            // Check if there is data for the current month in $counts
                            $found = false;
                            foreach ($counts as $count) {
                                $countMonthName = date('F', mktime(0, 0, 0, $count['month'], 1, $count['year']));
                                if ($countMonthName === $monthName) {
                                    $found = true;
                                    // Extract 'count_per_month' value as data
                                    $data[] = $count['count_per_month'];
                                    break;
                                }
                            }

                            // If no data for the current month in $counts, set count_per_month to 0
                            if (!$found) {
                                $data[] = 0;
                            }

                            // Check if there is data for the current month in $counts1
                            $found1 = false;
                            foreach ($counts1 as $count1) {
                                $countMonthName = date('F', mktime(0, 0, 0, $count1['month'], 1, $count1['year']));
                                if ($countMonthName === $monthName) {
                                    $found1 = true;
                                    // Extract 'count_per_month' value as data1
                                    $data1[] = $count1['count_per_month'];
                                    break;
                                }
                            }

                            // If no data for the current month in $counts1, set count_per_month to 0
                            if (!$found1) {
                                $data1[] = 0;
                            }
                            

                            // Add the month name to labels only once
                            $labels[] = $monthName;
                        }

                        echo "var labels = " . json_encode($labels) . ";";
                        echo "var data = " . json_encode($data) . ";";
                        echo "var data1 = " . json_encode($data1) . ";";
                    }
                    ?>

                    // Create the chart
                    var ctx = document.getElementById('myChart').getContext('2d');
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Reported Bite Case',
                                data: data1,
                                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                                borderColor: 'rgba(0, 123, 255, 1)',
                                borderWidth: 1,
                            },
                            {
                                label: 'Confirm Bite Case',
                                data: data,
                                backgroundColor: 'rgba(255, 0, 0, 0.5)',
                                borderColor: 'rgba(255, 0, 0, 1)',
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            </script>

<canvas id="pieChart" style="max-width: 300px; max-height: 300px;"></canvas>
<script>
    // ... (existing scripts)
    // Data for the pie chart
    <?php
// Initialize arrays for data2 and data3
$data2 = [];
$data3 = [];

// Check if there is data for the current year in $counts2
$found2 = false;
foreach ($counts2 as $count2) {
    // Extract 'count_per_year' value as data2
    $data2[Date('Y')] = $count2['count_per_year'];
    $found2 = true;
}

// If no data for the current year in $counts2, set count_per_year to 0
if (!$found2) {
    $data2[Date('Y')] = 0;
}

// Check if there is data for the current year in $counts3
$found3 = false;
foreach ($counts3 as $count3) {
    // Extract 'count_per_year' value as data3
    $data3[Date('Y')] = $count3['count_per_year'];
    $found3 = true;
}

// If no data for the current year in $counts3, set count_per_year to 0
if (!$found3) {
    $data3[Date('Y')] = 0;
}

// Calculate the percentage of $data3 with respect to $data2
$slicePercentage = ($data2[Date('Y')] > 0) ? ($data3[Date('Y')] / $data2[Date('Y')]) * 100 : 0;

// Calculate the percentage of the remaining data (whole pizza)
$remainingPercentage = 100 - $slicePercentage;

// Use $slicePercentage and $remainingPercentage in the JavaScript block
echo "var slicePercentage = " . json_encode($slicePercentage) . ";";
echo "var remainingPercentage = " . json_encode($remainingPercentage) . ";";
?>




// Create the pie chart
// Create the pie chart
// Create the pie chart
var ctxPie = document.getElementById('pieChart').getContext('2d');
var myPieChart = new Chart(ctxPie, {
    type: 'pie', // Use 'doughnut' type for a pie chart with a hole
    data: {
        labels: ['Reported Bite Case', 'Confirmed Bite Cases'],
        datasets: [{
            data: [remainingPercentage, slicePercentage],
            backgroundColor: ['rgba(255, 0, 0, 0.5)', 'rgba(0, 123, 255, 0.5)'],
            borderColor: ['rgba(255, 0, 0, 1)', 'rgba(0, 123, 255, 1)'],
            borderWidth: 1,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                fontColor: 'black',
            },
        },
        title: {
            display: true,
            text: 'Bite Case Distribution', // Set your desired title here
            fontSize: 18,
        },
    },
});
</script>

        </div>
    </div>
</body>
