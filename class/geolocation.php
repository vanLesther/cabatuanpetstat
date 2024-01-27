<?php
require_once "db_connect.php";

class Geolocation {
    public function saveGeolocation($latitude, $longitude) {
        global $conn;

        try {
            $stmt = $conn->prepare("INSERT INTO geolocation (latitude, longitude) VALUES (?, ?)");
            $stmt->bind_param("ss", $latitude, $longitude);
            $stmt->execute();

            // Check if the insertion was successful
            if ($stmt->affected_rows > 0) {
                // Geolocation stored successfully
                $geoID = $stmt->insert_id;
                return $geoID;
            } else {
                // Failed to store geolocation
                return false;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }

        $stmt->close();
    }
    public function updateGeoLocation($geoID) {
        global $conn;
    
        $query = "SELECT resident.name, barangay.barangay,resident.geoID, geolocation.latitude, geolocation.longitude FROM resident 
                  INNER JOIN barangay ON resident.brgyID = barangay.brgyID
                  INNER JOIN geolocation ON resident.geoID = geolocation.geoID
                  WHERE resident.geoID = $geoID";
    
        $result = $conn->query($query);
    
        if ($result && $result->num_rows > 0) {
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    
        return false;
    }
public function confirmUpdatedResidentLocation($geoID, $latitude, $longitude) {
    global $conn;
    
    $query = "UPDATE geolocation SET latitude = ?, longitude = ? WHERE geoID = ?";

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ddi", $latitude, $longitude, $geoID);

    $result = $stmt->execute();

    if ($result) {
            // Redirect to dashboard1.php with a success message
            header("Location: ./dashboard1.php?active-tab=2");
            exit; // Ensure no further code execution after the redirection
    }
    return false; // Return false if any step fails
}

public function confirmUpdatedBitesLocation($geoID, $latitude, $longitude) {
    global $conn;
    
    $query = "UPDATE geolocation SET latitude = ?, longitude = ? WHERE geoID = ?";

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ddi", $latitude, $longitude, $geoID);

    $result = $stmt->execute();

    if ($result) {
            // Redirect to dashboard1.php with a success message
            header("Location: ./dashboardBiteCases.php?active-tab=1");
            exit; // Ensure no further code execution after the redirection
    }
    return false; // Return false if any step fails
}
public function confirmUpdatedDeathLocation($geoID, $latitude, $longitude) {
    global $conn;
    
    $query = "UPDATE geolocation SET latitude = ?, longitude = ? WHERE geoID = ?";

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ddi", $latitude, $longitude, $geoID);

    $result = $stmt->execute();

    if ($result) {
            // Redirect to dashboard1.php with a success message
            header("Location: ./dashboardDeathCases.php?active-tab=1");
            exit; // Ensure no further code execution after the redirection
    }
    return false; // Return false if any step fails
}
public function confirmUpdatedSusLocation($geoID, $latitude, $longitude) {
    global $conn;
    
    $query = "UPDATE geolocation SET latitude = ?, longitude = ? WHERE geoID = ?";

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ddi", $latitude, $longitude, $geoID);

    $result = $stmt->execute();

    if ($result) {
            // Redirect to dashboard1.php with a success message
            header("Location: ./dashboardRabidCases.php?active-tab=1");
            exit; // Ensure no further code execution after the redirection
    }
    return false; // Return false if any step fails
}
public function updateBiteCaseLocation($geoID) {
    global $conn;

    $query = "SELECT barangay.barangay,case.caseGeoID, geolocation.latitude, geolocation.longitude FROM `case`
              INNER JOIN barangay ON case.brgyID = barangay.brgyID
              INNER JOIN geolocation ON case.caseGeoID = geolocation.geoID
              WHERE case.caseGeoID = $geoID";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    return false;
}
}

?>
