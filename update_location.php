<?php
// update_location.php

require_once("class/resident.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["geoID"])) {
    $geoID = $_POST["geoID"];

    $geolocation = new Geolocation();
    $location = $geolocation->updateGeoLocation($geoID);

    if ($location !== false) {
        // Return the location as JSON response
        echo json_encode($location);
    } else {
        echo json_encode(["error" => "Failed to update location"]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
