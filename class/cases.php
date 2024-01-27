<?php
require_once "db_connect.php";

class Cases {
        public function addBiteCase($residentID, $brgyID, $petID, $geoID, $victimsName, $caseType, $description, $date, $bpartBitten, $caseStatus) {
            global $conn;
            try {
                $stmt = $conn->prepare("INSERT INTO `case` (residentID, brgyID, petID, caseGeoID, victimsName, caseType, description, date, bpartBitten, caseStatus)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiisissii", $residentID, $brgyID, $petID, $geoID, $victimsName, $caseType, $description, $date, $bpartBitten, $caseStatus);
                $stmt->execute();
        
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    return true; // Case added successfully
                } else {
                    $stmt->close();
                    return "Failed to add the case to the database.";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false;
            }
        }
        public function addBiteCaseRes($residentID, $brgyID, $petID, $geoID, $victimsName, $caseType, $description, $date, $bpartBitten) {
            global $conn;
            try {
                $stmt = $conn->prepare("INSERT INTO `case` (residentID, brgyID, petID, caseGeoID, victimsName, caseType, description, date, bpartBitten)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiisissi", $residentID, $brgyID, $petID, $geoID, $victimsName, $caseType, $description, $date, $bpartBitten);
                $stmt->execute();
        
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    return true; // Case added successfully
                } else {
                    $stmt->close();
                    return "Failed to add the case to the database.";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false;
            }
        }
    
    // Helper function to get petID by pet name
    private function getPetIDByName($petName) {
        global $conn;
        $stmt = $conn->prepare("SELECT petID FROM pet WHERE pname = ? AND residentID = ?");
        $stmt->bind_param("si", $petName, $residentID);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($petID);
            $stmt->fetch();
            return $petID;
        }
    
        return false;
    }
    
    // Helper function to get ownerID by owner name
    private function getOwnerIDByName($ownerName) {
        global $conn;
        $stmt = $conn->prepare("SELECT residentID FROM resident WHERE name = ?");
        $stmt->bind_param("s", $ownerName);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($ownerID);
            $stmt->fetch();
            return $ownerID;
        }
    
        return false;
    }
    public function addUnknown($residentID, $brgyID, $geoID, $victimsName, $caseType, $description, $date, $bpartBitten, $caseStatus) {
        global $conn;
        try {
            $stmt = $conn->prepare("INSERT INTO `case` (residentID, brgyID, caseGeoID, victimsName, caseType, description, date, bpartBitten, caseStatus)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisissii", $residentID, $brgyID, $geoID, $victimsName, $caseType, $description, $date, $bpartBitten, $caseStatus);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true; // Case added successfully
            } else {
                $stmt->close();
                return "Failed to add the case to the database.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function addSuspectedCase($residentID, $brgyID, $petID, $geoID, $caseType, $description, $date, $caseStatus) {
        global $conn;
        try {
            $stmt = $conn->prepare("INSERT INTO `case` (residentID, brgyID, petID, caseGeoID, caseType, description, date, caseStatus)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiissi", $residentID, $brgyID, $petID, $geoID, $caseType, $description, $date, $caseStatus);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true; // Case added successfully
            } else {
                $stmt->close();
                return "Failed to add the case to the database.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function addUnknownSus($residentID, $brgyID, $geoID, $caseType, $description, $date, $caseStatus) {
        global $conn;
        try {
            $stmt = $conn->prepare("INSERT INTO `case` (residentID, brgyID, caseGeoID, caseType, description, date, caseStatus)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiissi", $residentID, $brgyID, $geoID, $caseType, $description, $date, $caseStatus);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true; // Case added successfully
            } else {
                $stmt->close();
                return "Failed to add the case to the database.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function addSuspectedCaseRes($residentID, $brgyID, $petID, $geoID, $caseType, $description, $date) {
        global $conn;
        try {
            $stmt = $conn->prepare("INSERT INTO `case` (residentID, brgyID, petID, caseGeoID, caseType, description, date)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiiss", $residentID, $brgyID, $petID, $geoID, $caseType, $description, $date);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true; // Case added successfully
            } else {
                $stmt->close();
                return "Failed to add the case to the database.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getSuspectedCase($residentID) {
        global $conn;
    
        try {
            $stmt = $conn->prepare("SELECT c.*, p.*, r.*
            FROM `case` AS c
            LEFT JOIN `resident` AS r ON r.residentID = c.residentID
            LEFT JOIN `pet` AS p ON p.petID = c.petID
            WHERE c.residentID = ?
            AND c.caseType = 2;
                                    ;");
            $stmt->bind_param("i", $residentID);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Fetch all pets as an associative array
            $pets = $result->fetch_all(MYSQLI_ASSOC);
    
            $stmt->close();
            return $pets;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function getAllNewBiteCase($brgyID) {
        global $conn;
    
        $query = "SELECT * FROM `case`  as c LEFT JOIN pet as p ON c.petID = p.petID WHERE c.caseStatus = 0 AND caseType = 0 AND c.brgyID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $brgyID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            return false; // Return false if the query fails
        }
    
        return $result;
    }
    
    public function getAllValidBiteCase($brgyID) {
        global $conn;
    
        $query = "SELECT * FROM `case`  as c LEFT JOIN pet as p ON c.petID = p.petID WHERE c.caseStatus = 1 AND caseType = 0 AND c.brgyID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $brgyID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            return false; // Return false if the query fails
        }
    
        return $result;
    }
    
    public function getAllRejectedBiteCase($brgyID) {
        global $conn;
    
        $query = "SELECT * FROM `case`  as c LEFT JOIN pet as p ON c.petID = p.petID WHERE c.caseStatus = 2 AND caseType = 0 AND c.brgyID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $brgyID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            return false; // Return false if the query fails
        }
    
        return $result;
    }
    
    

public function updateBiteCaseStatus($caseID, $caseStatus){
    global $conn;

    $stmt = $conn->prepare("UPDATE `case` SET caseStatus = ? WHERE caseID = ?");
    $stmt->bind_param("ii", $caseStatus, $caseID);

    try {
        if ($stmt->execute()) {
            // Update successful
            return true;
        } else {
            // Failed to update user status
            return "Failed to update user status: " . $stmt->error;
        }
    } catch (Exception $e) {
        // Handle the exception
        return "Failed to update user status: " . $e->getMessage();
    } finally {
        $stmt->close();
    }
}
public function getAllNewDeathCase($brgyID) {
    global $conn;

    $query = "SELECT c.*, p.* FROM `case` c JOIN pet p ON c.petID = p.petID WHERE c.caseStatus = 0 AND c.caseType = 1 AND c.brgyID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}

public function getAllValidDeathCase($brgyID) {
    global $conn;

    $query = "SELECT c.*, p.* FROM `case` c JOIN pet p ON c.petID = p.petID WHERE c.caseStatus = 1 AND c.caseType = 1 AND c.brgyID = ?;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}

public function getAllRejectedDeathCase($brgyID) {
    global $conn;

    $query = "SELECT c.*, p.* FROM `case` c JOIN pet p ON c.petID = p.petID WHERE c.caseStatus = 2 AND c.caseType = 1 AND c.brgyID = ?;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}



public function updateDeathCaseStatus($caseID, $caseStatus){
global $conn;

$stmt = $conn->prepare("UPDATE `case` SET caseStatus = ?  WHERE caseID = ?");
$stmt->bind_param("ii", $caseStatus, $caseID);

try {
    if ($stmt->execute()) {
        // Update successful
        return true;
    } else {
        // Failed to update user status
        return "Failed to update user status: " . $stmt->error;
    }
} catch (Exception $e) {
    // Handle the exception
    return "Failed to update user status: " . $e->getMessage();
} finally {
    $stmt->close();
}
}
public function addDeathCase($residentID, $brgyID, $petID, $geoID, $caseType, $description, $cdate, $CRabies, $caseStatus) {
    global $conn; 

    try {
        // $petID = $this->getPetIDByName($petName);

        // if (!$petID) {
        //     return "Pet or owner not found in the database.";
        // }
        $stmt = $conn->prepare("INSERT INTO `case` (residentID, brgyID, petID, caseGeoID, caseType, description, date, confirmedRabies, caseStatus)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiissii", $residentID, $brgyID, $petID, $geoID, $caseType, $description, $cdate, $CRabies, $caseStatus);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true; // Case added successfully
        } else {
            $stmt->close();
            return "Failed to add the case to the database.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

public function addDeathCaseRes($residentID, $brgyID, $petID, $geoID, $caseType, $description, $cdate, $CRabies) {
    global $conn; 

    try {
        // $petID = $this->getPetIDByName($petName);

        // if (!$petID) {
        //     return "Pet or owner not found in the database.";
        // }
        $stmt = $conn->prepare("INSERT INTO `case` (residentID, brgyID, petID, caseGeoID, caseType, description, date, confirmedRabies)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiissi", $residentID, $brgyID, $petID, $geoID, $caseType, $description, $cdate, $CRabies);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true; // Case added successfully
        } else {
            $stmt->close();
            return "Failed to add the case to the database.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
public function getBitesByResidentID($residentID) {
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT c.*, p.*, r.*
        FROM `case` AS c
        LEFT JOIN `resident` AS r ON r.residentID = c.residentID
        LEFT JOIN `pet` AS p ON p.petID = c.petID
        WHERE c.residentID = ?
        AND c.caseType = 0
        ");
        $stmt->bind_param("i", $residentID);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all pets as an associative array
        $pets = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        return $pets;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
public function getDeathByResidentID($residentID) {
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT c.*, p.*
                                FROM `case` AS c
                                INNER JOIN `pet` AS p ON p.petID = c.petID
                                WHERE c.residentID = ? AND c.caseType = 1;
                                ;");
        $stmt->bind_param("i", $residentID);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all pets as an associative array
        $pets = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        return $pets;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
public function getAllValidBiteCaseByBrgy($brgyID) {
    global $conn;

    $query = "SELECT c.*, p.*, r.*, b.*
    FROM `case` AS c
    INNER JOIN pet AS p ON c.petID = p.petID
    INNER JOIN resident AS r ON p.residentID = r.residentID
    INNER JOIN barangay AS b ON c.brgyID = b.brgyID
    WHERE c.caseStatus = 1 AND c.caseType = 0 AND c.brgyID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}


    public function cancelBite($caseID) {
        global $conn;
        $conn->begin_transaction();
        try{
        $stmt = $conn->prepare("DELETE FROM `case` WHERE caseID = ?");
        $stmt->bind_param("i", $caseID);
        $stmt->execute();
       
    
            // Commit the transaction
            $conn->commit();
    
            return true;
        } catch (Exception $e) {
            // An error occurred, rollback the transaction
            $conn->rollback();
            return "Error: " . $e->getMessage();
        }
    }
    public function cancelDeath($caseID) {
        global $conn;
        $conn->begin_transaction();
        try{
        $stmt = $conn->prepare("DELETE FROM `case` WHERE caseID = ?");
        $stmt->bind_param("i", $caseID);
        $stmt->execute();
       
    
            // Commit the transaction
            $conn->commit();
    
            return true;
        } catch (Exception $e) {
            // An error occurred, rollback the transaction
            $conn->rollback();
            return "Error: " . $e->getMessage();
        }
    }
    public function getDeathByBrgy($brgyID) {
        global $conn;
    
        $query = "SELECT c.*, p.*, r.name, p.pname, b.barangay FROM `case` as c NATURAL JOIN pet as p NATURAL JOIN resident as r INNER JOIN barangay as b ON r.brgyID = b.brgyID WHERE c.caseStatus = 1 AND c.caseType = 1 AND c.brgyID = ?
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $brgyID);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
        return false; // Return false if the query fails
        }

        return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        public function getRabidByBrgy($brgyID) {
            global $conn;
        
            $query = "SELECT c.*, p.*, r.*, b.* 
            FROM pet AS p
            INNER JOIN resident AS r ON p.residentID = r.residentID
            INNER JOIN `case` AS c ON c.petID = p.petID
            INNER JOIN barangay AS b ON c.brgyID = b.brgyID
            WHERE c.caseStatus = 1 AND c.caseType = 2 AND c.brgyID = ?";
    
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $brgyID);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if (!$result) {
            return false; // Return false if the query fails
            }
    
            return $result->fetch_all(MYSQLI_ASSOC);
            }
public function viewBiteCaseLocation($caseID) {
    global $conn;

    // Use a prepared statement to avoid SQL injection
    $query = "SELECT C.caseGeoID, G.latitude, G.longitude, C.victimsName, B.barangay
        FROM `case` C
        INNER JOIN geolocation G ON C.caseGeoID = G.geoID
        INNER JOIN barangay B ON C.brgyID = B.brgyID
        WHERE C.caseGeoID = ?";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        return false; // Return false if the prepare fails
    }

    // Bind the parameter and execute the query
    $stmt->bind_param("i", $caseID);
    $stmt->execute();

    // Get the result
    $bite = $stmt->get_result();

    // Close the statement
    $stmt->close();

    return $bite;
}
public function viewCaseLocation($caseID) {
    global $conn;

    $query = "SELECT C.caseGeoID, G.latitude, G.longitude, P.pname, B.barangay
        FROM `case` C
        INNER JOIN geolocation G ON C.caseGeoID = G.geoID
        INNER JOIN barangay B ON C.brgyID = B.brgyID
        LEFT JOIN pet P ON C.petID = P.petID
        WHERE C.caseID = $caseID";

    $result = $conn->query($query);

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}
    public function updateRabies($caseID, $confirmedRabies){
    global $conn;

    $stmt = $conn->prepare("UPDATE `case` SET confirmedRabies = 1 WHERE caseID = ?");
    $stmt->bind_param("i", $caseID);

    try {
        if ($stmt->execute()) {
            // Update successful
            return true;
        } else {
            // Failed to update user status
            return "Failed to update rabies status: " . $stmt->error;
        }
    } catch (Exception $e) {
        // Handle the exception
        return "Failed to update rabies status: " . $e->getMessage();
    } finally {
        $stmt->close();
    }
}
    public function getAllValidCaseByBarangay1($brgyID) {
    global $conn;

    $stmt = $conn->prepare("SELECT G.geoID, G.latitude, G.longitude
    FROM pet P INNER JOIN resident R ON P.residentID=R.residentID INNER JOIN geolocation G ON R.geoID=G.geoID INNER JOIN `case` C ON R.brgyID=C.brgyID WHERE R.brgyID=?;");
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();

    return $stmt->get_result(); // Return the result set, not just a single row
}
    public function getNewSus($brgyID) {
    global $conn;

    $query = "SELECT c.*, p.*, r.*
    FROM `case` c
    INNER JOIN pet AS p ON p.petID = c.petID
    INNER JOIN resident AS r ON p.residentID = r.residentID
    WHERE c.caseStatus = 0 AND c.caseType = 2 AND c.brgyID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}
public function getValidSus($brgyID) {
    global $conn;

    $query = "SELECT * FROM `case`  as c LEFT JOIN pet as p ON c.petID = p.petID LEFT JOIN resident as r ON r.residentID = c.residentID WHERE c.caseStatus = 1 AND caseType = 2 AND c.brgyID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}
public function getAllValidCaseByBarangay($brgyID) {
    global $conn;

    $stmt = $conn->prepare("SELECT C.caseGeoID, G.latitude, G.longitude
    FROM `case` C INNER JOIN geolocation G ON C.caseGeoID = G.geoID
    WHERE caseType= 0 AND C.brgyID = ?;");
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();

    return $stmt->get_result(); // Return the result set, not just a single row
} 
public function getRejectedSus($brgyID) {
    global $conn;

    $query = "SELECT c.*, p.*, r.*
    FROM `case` c
    INNER JOIN pet AS p ON p.petID = c.petID
    INNER JOIN resident AS r ON p.residentID = r.residentID
    WHERE c.caseStatus = 2 AND c.caseType = 2 AND c.brgyID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}
function countCase($brgyID) {
    global $conn;

    // Query to count cases per month for a specific brgyID
    $sql = "SELECT 
                EXTRACT(MONTH FROM date) AS month,
                EXTRACT(YEAR FROM date) AS year,
                COUNT(*) AS count_per_month
            FROM 
                `case`
            WHERE 
                brgyID = ? AND caseType = 0 AND caseStatus= 1
            GROUP BY 
                EXTRACT(MONTH FROM date), EXTRACT(YEAR FROM date)
            ORDER BY 
                year, month";

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $brgyID); // "i" indicates an integer parameter

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the result into an array
    $counts = [];
    while ($row = $result->fetch_assoc()) {
        $counts[] = $row;
    }

    // Close the prepared statement
    $stmt->close();

    return $counts;
}
function countAllCase($brgyID) {
    global $conn;

    // Query to count cases per month for a specific brgyID
    $sql = "SELECT 
                EXTRACT(MONTH FROM date) AS month,
                EXTRACT(YEAR FROM date) AS year,
                COUNT(*) AS count_per_month
            FROM 
                `case`
            WHERE 
                brgyID = ? AND caseType = 0
            GROUP BY 
                EXTRACT(MONTH FROM date), EXTRACT(YEAR FROM date)
            ORDER BY 
                year, month";

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $brgyID); // "i" indicates an integer parameter

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the result into an array
    $counts1 = [];
    while ($row = $result->fetch_assoc()) {
        $counts1[] = $row;
    }

    // Close the prepared statement
    $stmt->close();

    return $counts1;
}
function countAllCasePerYear($brgyID) {
    global $conn;

    // Query to count cases per month for a specific brgyID
    $sql = "SELECT 
    EXTRACT(YEAR FROM date) AS year,
    COUNT(*) AS count_per_year
FROM 
    `case`
WHERE 
    brgyID = ? AND caseType = 0 AND YEAR(date) = YEAR(CURRENT_DATE)
GROUP BY 
    EXTRACT(YEAR FROM date)
ORDER BY 
    year
";

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $brgyID); // "i" indicates an integer parameter

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the result into an array
    $counts2 = [];
    while ($row = $result->fetch_assoc()) {
        $counts2[] = $row;
    }

    // Close the prepared statement
    $stmt->close();

    return $counts2;
}
function countAllValidCasePerYear($brgyID) {
    global $conn;

    // Query to count cases per month for a specific brgyID
    $sql = "SELECT 
    EXTRACT(YEAR FROM date) AS year,
    COUNT(*) AS count_per_year
FROM 
    `case`
WHERE 
    brgyID = ? AND caseType = 0 AND caseStatus = 1 AND  YEAR(date) = YEAR(CURRENT_DATE)
GROUP BY 
    EXTRACT(YEAR FROM date)
ORDER BY 
    year
";

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $brgyID); // "i" indicates an integer parameter

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the result into an array
    $counts3 = [];
    while ($row = $result->fetch_assoc()) {
        $counts3[] = $row;
    }

    // Close the prepared statement
    $stmt->close();

    return $counts3;
}
}
?>
