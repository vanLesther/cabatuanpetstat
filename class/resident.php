<?php
require_once "db_connect.php";

class Resident {

        public function checkEmailExists($email) {
            global $conn;
    
            $stmt = $conn->prepare("SELECT email FROM resident WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
    
            return $stmt->num_rows > 0;
        }
    
    function registerResident($name, $geoID, $brgyID, $contactNo, $email, $password) {
        global $conn;
    
        // Check if email already exists
        $resident = new Resident();
        if ($resident->checkEmailExists($email)) {
            return "Email already registered";
        }
    
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $conn->prepare("INSERT INTO resident (name, geoID, brgyID, contactNo, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $geoID, $brgyID, $contactNo, $email, $hashedPassword);
    
        if ($stmt->execute()) {
            // Registration successful
            return true;
        } else {
            // Failed to register resident
            return "Registration failed: " . $stmt->error;
        }
    }
    function createAcc($name, $geoID, $brgyID, $contactNo, $email, $password, $userStatus) {
        global $conn;
        
        // Check if email already exists
        $resident = new Resident();
        if ($resident->checkEmailExists($email)) {
            return "Email already registered";
        }
        
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO resident (name, geoID, brgyID, contactNo, email, password, userStatus) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $geoID, $brgyID, $contactNo, $email, $hashedPassword, $userStatus);
        
        if ($stmt->execute()) {
            // Registration successful
            return true;
        } else {
            // Failed to register resident
            return "Registration failed: " . $stmt->error;
        }
    }
    
    function loginResident($email, $password) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM resident WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            // User not found
            return false;
        }
    
        $user = $result->fetch_assoc();
    
        if (password_verify($password, $user['password'])) {
            // Password is correct, remove the password field and return the user data
            unset($user['password']);
            return $user;
        } else {
            // Incorrect password
            return false;
        }
    }

function updateUserStatus($residentID, $status) {
    global $conn;

    $stmt = $conn->prepare("UPDATE resident SET userStatus = ? WHERE residentID = ?");
    $stmt->bind_param("ii", $status, $residentID);

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
public function getAllNewResidents($brgyID) {
    global $conn;

    $query = "SELECT * FROM resident WHERE userStatus = 0 AND brgyID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}

public function getAllValidResidents($brgyID) {
    global $conn;

    $query = "SELECT * FROM resident WHERE userStatus = 1 AND brgyID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}
public function getAllRejectedResidents($brgyID) {
    global $conn;

    $query = "SELECT * FROM resident WHERE userStatus = 2 AND brgyID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}
public function viewResidentLocation($userID) {
    global $conn;

    $query = "SELECT R.geoID, G.latitude, G.longitude, R.name, B.barangay
        FROM resident R
        INNER JOIN geolocation G ON R.geoID = G.geoID
        INNER JOIN barangay B ON R.brgyID = B.brgyID
        WHERE R.residentID = $userID";

    $result = $conn->query($query);

    if (!$result) {
        return false; // Return false if the query fails
    }

    return $result;
}

public function getAllValidResidentByBarangay($brgyID) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM resident WHERE brgyID = ? AND userStatus IN (0, 1)");
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();

    return $stmt->get_result(); // Return the result set, not just a single row
}
public function getOfficersByBarangay($brgyID) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM resident WHERE brgyID = ? AND userType = 1");
    $stmt->bind_param("i", $brgyID);
    $stmt->execute();

    return $stmt->get_result();
}
public function isResidentOfficerInBarangay($residentID, $brgyID) {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM resident WHERE residentID = ? AND brgyID = ? AND userType = 1");
    $stmt->bind_param("ii", $residentID, $brgyID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    return $count > 0;
}
public function revokeOfficer($residentID, $brgyID) {
    global $conn;

    $stmt = $conn->prepare("UPDATE resident SET userType = 0 WHERE brgyID = ? AND residentID = ?");
    $stmt->bind_param("ii", $brgyID, $residentID); // Change the order of parameters here

    if ($stmt->execute()) {
        return true; // Successful revocation
    } else {
        return false; // Failed revocation
    }
}
public function assignOfficer($residentID, $brgyID) {
    global $conn;

    $stmt = $conn->prepare("UPDATE resident SET userType = 1, userStatus = 1 WHERE residentID = ? AND brgyID = ?;");
    $stmt->bind_param("ii", $residentID, $brgyID);
    
    if ($stmt->execute()) {
        return true; // Successful assignment
    } else {
        return false; // Failed assignment
    }
}
public function getAllOfficers() {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM resident WHERE userType = 1");
    $stmt->execute();

    return $stmt->get_result();

}
public function getAccName($residentID){
    global $conn;

    $stmt = $conn->prepare("SELECT name FROM resident WHERE residentID = ?");
    $stmt->bind_param("i", $residentID);
    
    if ($stmt->execute()) {
        return true; // Successful assignment
    } else {
        return false; // Failed assignment
    }

}

}
?>
