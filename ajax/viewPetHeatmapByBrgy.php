<!-- <?php
// require_once("../class/db_connect.php"); // Adjust the path if needed

// // Assuming you have a function to establish a database connection
// global $conn; // Modify this function based on your implementation

// if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['brgyid'])) {
//     $brgyID = $_POST['brgyid'];

//     // Fetch locations from the database for the selected barangay
//     $sql = "SELECT latitude, longitude FROM pet AS P
//             INNER JOIN resident AS R ON P.residentID = R.residentID
//             INNER JOIN geolocation AS G ON R.geolocationID = G.geolocationID
//             WHERE R.brgyID = ?";
    
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $brgyID);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $locations = array();

//     while ($row = $result->fetch_assoc()) {
//         $locations[] = $row;
//     }

//     echo json_encode($locations);
// } else {
//     // Invalid request, handle accordingly
//     echo json_encode(array('error' => 'Invalid request'));
// }

// // Close the database connection
// $stmt->close();
// $conn->close();
?> -->
