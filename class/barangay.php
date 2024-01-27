<?php
require_once "db_connect.php";

class Barangay {
    public function getBrgyID($barangay) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT brgyID FROM barangay WHERE barangay = ?");
            $stmt->bind_param("s", $barangay);
            $stmt->execute();
            $stmt->bind_result($brgyID);
            $stmt->fetch();
            
            $stmt->close();
            
            return $brgyID;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function getBrgyName($brgyID) {
        global $conn;
    
        try {
            $stmt = $conn->prepare("SELECT barangay FROM barangay WHERE brgyID = ?");
            $stmt->bind_param("i", $brgyID);  // Use "i" for integer
            $stmt->execute();
            $stmt->bind_result($barangayName);
            $stmt->fetch();
    
            $stmt->close();
    
            return $barangayName;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function getBrgys() {
        global $conn;

        try {
            $stmt = $conn->query("SELECT * FROM barangay");
            return $stmt->fetch_All();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function specgetBrgy($brgyID) {
        global $conn;

        try {
            $stmt = $conn->prepare("SELECT * FROM barangay WHERE brgyID != ?");
            $stmt->bind_param("i", $brgyID); 
            $stmt->execute();
            return $stmt->get_result();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function getBrgyLocation($brgyID) {
        global $conn;
    
        try {
            $stmt = $conn->prepare("SELECT B.barangay, G.latitude, G.longitude FROM barangay B INNER JOIN geolocation G ON B.geoID = G.geoID WHERE B.brgyID = ?");
            $stmt->bind_param("i", $brgyID); // Assuming $brgyID is an integer
            $stmt->execute();
    
            // Fetch associative array
            $result = $stmt->get_result();
            $locationData = $result->fetch_all(MYSQLI_ASSOC);
    
            $stmt->close();
            
            return $locationData;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}

?>
