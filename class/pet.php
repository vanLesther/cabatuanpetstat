<?php
require_once "db_connect.php";

class Pet {
    private $conn;


    public function addPet($residentID, $petType, $pname, $sex, $neutering, $color, $age, $regDate, $currentVac, $statusVac, $pdescription, $status) {
        global $conn;
    
        // Check if the pet already exists
        $existingPet = $this->getPetByDetails($residentID, $pname, $sex, $color, $age);
        if ($existingPet) {
            return true; // Pet already exists, return success
        }
    
        // Prepare the SQL statement for inserting into the "pet" table
        $stmt = $conn->prepare("INSERT INTO pet (residentID, petType, pname, sex, neutering, color, age, regDate, currentVac, statusVac, pdescription, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
        if ($stmt) {
            // Bind the parameters and execute the query
            $stmt->bind_param("iisiisissssi", $residentID, $petType, $pname, $sex, $neutering, $color, $age, $regDate, $currentVac, $statusVac, $pdescription, $status);
    
            if ($stmt->execute()) {
                // Check if the insertion was successful
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    return true; // Pet added successfully
                }
            }
            $stmt->close();
        }
        return false; // Failed to add pet
    }
    
    public function addPetRes($residentID, $petType, $pname, $sex, $neutering, $color, $age, $regDate, $statusVac, $pdescription) {
        global $conn;
    
        // Check if the pet already exists
        $existingPet = $this->getPetByDetails($residentID, $pname, $sex, $color, $age);
        if ($existingPet) {
            return true; // Pet already exists, return success
        }
    
        // Prepare the SQL statement for inserting into the "pet" table
        $stmt = $conn->prepare("INSERT INTO pet (residentID, petType, pname, sex, neutering, color, age, regDate, statusVac, pdescription) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
        if ($stmt) {
            // Bind the parameters and execute the query
            $stmt->bind_param("iisiisisss", $residentID, $petType, $pname, $sex, $neutering, $color, $age, $regDate, $statusVac, $pdescription);
    
            if ($stmt->execute()) {
                // Check if the insertion was successful
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    return true; // Pet added successfully
                }
            }
            $stmt->close();
        }
        return false; // Failed to add pet
    }
    
        private function getPetByDetails($residentID, $pname, $sex, $color, $age) {
            global $conn;
        
            $stmt = $conn->prepare("SELECT * FROM pet WHERE residentID = ? AND pname = ? AND sex = ? AND color = ? AND age = ?");
            $stmt->bind_param("issis", $residentID, $pname, $sex, $color, $age);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
        
            return null;
        }
    
    public function getPetsByResidentID($residentID) {
        global $conn;
    
        try {
            $stmt = $conn->prepare("SELECT p.*, r.*, v.*, p.petID
            FROM resident AS r
            NATURAL JOIN pet AS p
            LEFT JOIN (
                SELECT petID, MAX(lastVaccination) AS maxlastVaccination, lastVaccination
                FROM vaccination
                GROUP BY petID
            ) AS v ON p.petID = v.petID
            WHERE r.residentID = ?
            ORDER BY v.maxlastVaccination DESC");
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
    
    
    public function getAllNewPets($brgyID){
        global $conn;

        $query = "SELECT p.*, r.*, v.*, p.petID
        FROM resident AS r
        NATURAL JOIN pet AS p
        LEFT JOIN (
            SELECT petID, MAX(lastVaccination) AS maxlastVaccination, lastVaccination
            FROM vaccination
            GROUP BY petID
        ) AS v ON p.petID = v.petID
        WHERE p.status = 0 AND brgyID = ?
        ORDER BY v.maxlastVaccination DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $brgyID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            return false; // Return false if the query fails
        }
    
        return $result;
    }

    public function getAllValidPets($brgyID) {
    global $conn;

    $query = "SELECT p.*, r.*, v.*, p.petID
    FROM resident AS r
    NATURAL JOIN pet AS p
    LEFT JOIN (
        SELECT petID, MAX(lastVaccination) AS maxlastVaccination, lastVaccination
        FROM vaccination
        GROUP BY petID
    ) AS v ON p.petID = v.petID
    WHERE p.status = 1 AND brgyID = ?
    ORDER BY v.maxlastVaccination DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false;
    }

    return $result;
}


    public function getAllRejectedPets($brgyID){
        global $conn;

        $query = "SELECT p.*, r.*, v.*, p.petID
        FROM resident AS r
        NATURAL JOIN pet AS p
        LEFT JOIN (
            SELECT petID, MAX(lastVaccination) AS maxlastVaccination, lastVaccination
            FROM vaccination
            GROUP BY petID
        ) AS v ON p.petID = v.petID
        WHERE p.status = 2 AND brgyID = ?
        ORDER BY v.maxlastVaccination DESC
        limit 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $brgyID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            return false; // Return false if the query fails
        }
    
        return $result;
    }
    public function updatePetStatus($petID, $status){
        global $conn;

        $stmt = $conn->prepare("UPDATE pet SET status = ? WHERE petID = ?");
        $stmt->bind_param("ii", $status, $petID);

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
    public function getRegistries($brgyID) {
        global $conn;
    
        try {
            $stmt = $conn->prepare("SELECT p.*, r.*, v.*, b.*, p.petID
            FROM resident AS r
            NATURAL JOIN pet AS p
            LEFT JOIN (
                SELECT petID, MAX(lastVaccination) AS maxlastVaccination, lastVaccination
                FROM vaccination
                GROUP BY petID
            ) AS v ON p.petID = v.petID
            LEFT JOIN barangay AS b ON r.brgyID = b.brgyID
            WHERE p.status = 1 AND r.brgyID = ?
            ORDER BY v.maxlastVaccination DESC;
            ");
            $stmt->bind_param("i", $brgyID);
            $stmt->execute();
            
    
            $result = $stmt->get_result();
    
            // Fetch the data into an associative array
            $registries = [];
            while ($row = $result->fetch_assoc()) {
                $registries[] = $row;
            }
    
            $stmt->close();
            return $registries;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function cancelReg($petID) {
        global $conn;
    
        try {
            // Start a transaction
            $conn->begin_transaction();
    
            // Delete records from the vaccination table first
            $stmtDeleteVaccination = $conn->prepare("DELETE FROM vaccination WHERE petID = ?");
            $stmtDeleteVaccination->bind_param("i", $petID);
            $stmtDeleteVaccination->execute();
    
            // Check for errors after execution
            if ($stmtDeleteVaccination->error) {
                throw new Exception("Error deleting vaccination records: " . $stmtDeleteVaccination->error);
            }
    
            $stmtDeleteVaccination->close();
    
            // Then, delete the record from the pet table
            $stmtDeletePet = $conn->prepare("DELETE FROM pet WHERE petID = ?");
            $stmtDeletePet->bind_param("i", $petID);
            $stmtDeletePet->execute();
    
            // Check for errors after execution
            if ($stmtDeletePet->error) {
                throw new Exception("Error deleting pet record: " . $stmtDeletePet->error);
            }
    
            $stmtDeletePet->close();
    
            // Commit the transaction
            $conn->commit();
    
            return true;
        } catch (Exception $e) {
            // An error occurred, rollback the transaction
            $conn->rollback();
            return "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
        }
    }
    public function updateVacStatus($petID, $currentVac) {
        global $conn;
    
        // Start a transaction
        $conn->begin_transaction();
    
        try {
            // Retrieve the current vaccination data
            $stmtSelectCurrentVac = $conn->prepare("SELECT * FROM pet WHERE petID = ?");
            $stmtSelectCurrentVac->bind_param("i", $petID);
            $stmtSelectCurrentVac->execute();
            $result = $stmtSelectCurrentVac->get_result();
            $currentVacData = $result->fetch_assoc();
            $stmtSelectCurrentVac->close();
    
            // Insert the current vaccination data into the history table
            $stmtInsertHistory = $conn->prepare("INSERT INTO vaccination (petID, lastVaccination) VALUES (?, ?)");
            $stmtInsertHistory->bind_param("is", $currentVacData['petID'], $currentVacData['currentVac']);
            if (!$stmtInsertHistory->execute()) {
                throw new Exception("Insert into vaccination failed: " . $stmtInsertHistory->error);
            }
            $stmtInsertHistory->close();
    
            // Update the current vaccination status in the main table
            $stmtUpdateVacStatus = $conn->prepare("UPDATE pet SET currentVac = ? WHERE petID = ?");
            $stmtUpdateVacStatus->bind_param("si", $currentVac, $petID);
            if (!$stmtUpdateVacStatus->execute()) {
                throw new Exception("Update pet table failed: " . $stmtUpdateVacStatus->error);
            }
            $stmtUpdateVacStatus->close();
    
            // Commit the transaction
            if (!$conn->commit()) {
                throw new Exception("Transaction commit failed: " . $conn->error);
            }
    
            return true;
        } catch (Exception $e) {
            // An error occurred, rollback the transaction
            $conn->rollback();
            return "Error: " . $e->getMessage();
        }
    }
    public function editPet($petID, $petType, $pname, $sex, $neutering, $color, $age) {
        global $conn;
    
        $stmt = $conn->prepare("UPDATE pet SET petType = ?, pname = ?, sex = ?, Neutering = ?, color = ?, age = ? WHERE petID = ?");
        $stmt->bind_param("issisii", $petType, $pname, $sex, $neutering, $color, $age, $petID);
    
        try {
            if ($stmt->execute()) {
                // Update successful
                return true;
            } else {
                // Failed to update pet
                return "Failed to update pet: " . $stmt->error;
            }
        } catch (Exception $e) {
            // Handle the exception
            return "Failed to update pet: " . $e->getMessage();
        } finally {
            $stmt->close();
        }
    }

    public function getVaccinations($petID){
        global $conn;

        $query = "SELECT p.*, r.*, v.*, p.petID
        FROM resident AS r
        NATURAL JOIN pet AS p
        LEFT JOIN (
            SELECT petID, MAX(lastVaccination) AS maxlastVaccination, lastVaccination
            FROM vaccination
            GROUP BY petID
        ) AS v ON p.petID = v.petID
        WHERE petID = ?
        ORDER BY v.maxlastVaccination DESC
        limit 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $petID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            return false; // Return false if the query fails
        }
    
        return $result;
    }
    public function getVacByPetID($residentID){
        global $conn;

        $query = "SELECT p.*, r.*, v.*, p.petID
        FROM resident AS r
        NATURAL JOIN pet AS p
        LEFT JOIN (
            SELECT petID, lastVaccination
            FROM vaccination
            GROUP BY petID
        ) AS v ON p.petID = v.petID
        WHERE residentID = ?
        ORDER BY lastVaccination DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $petID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result) {
            return false; // Return false if the query fails
        }
    
        return $result;
    }
    // public function searchDesc($query) {
    //   global $conn;

    //     $sql = "SELECT * FROM pet WHERE pdescription LIKE '%$query%'";
    //     $result = $conn->query($sql);

    //     if ($result->num_rows > 0) {
    //         return $result->fetch_all(MYSQLI_ASSOC);
    //     } else {
    //         return [];
    //     }
    // }
}
?>