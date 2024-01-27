<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add jQuery library -->

    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.heat"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function handlePasswordVerification() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;

    var passwordMatch = password === confirmPassword;

    var passwordIcon = document.getElementById("passwordStatusIcon");
    var confirmPasswordIcon = document.getElementById("confirmPasswordStatusIcon");

    passwordIcon.innerHTML = passwordMatch ? "<i class='bi bi-check-circle-fill text-success'></i>" : "<i class='bi bi-x-circle-fill text-danger'></i>";
    confirmPasswordIcon.innerHTML = passwordMatch ? "<i class='bi bi-check-circle-fill text-success'></i>" : "<i class='bi bi-x-circle-fill text-danger'></i>";

    document.getElementById("registerButton").disabled = !passwordMatch;
}
    </script>
     <style>
        #map {
            height: 50vh;
            width: 100%;
            margin: 20px auto;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registration Form</h1>
        <form method="POST" action="process_registration.php" id="registrationForm">
            <div class="mb-3">
                <label for="name" class="form-label">Name: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="mb-3">
                <label for="barangaySelect" class="form-label">Barangay: <span class="text-danger">*</span></label>
                <select id="barangaySelect" class="form-select" name="barangay" onchange="handleBarangaySelection()" required>
                    <option value="">Select Barangay</option>
                    <option value="Acao">Acao</option>
                    <option value="Amerang">Amerang</option>
                    <option value="Amurao">Amurao</option>
                    <option value="Anuang">Anuang</option>
                    <option value="Ayaman">Ayaman</option>
                    <option value="Ayong">Ayong</option>
                    <option value="Bacan">Bacan</option>
                    <option value="Balabag">Balabag</option>
                    <option value="Baluyan">Baluyan</option>
                    <option value="Banguit">Banguit</option>
                    <option value="Bulay">Bulay</option>
                    <option value="Cadoldolan">Cadoldolan</option>
                    <option value="Cagban">Cagban</option>
                    <option value="Calawagan">Calawagan</option>
                    <option value="Calayo">Calayo</option>
                    <option value="Duyan-Duyan">Duyan-Duyan</option>
                    <option value="Gaub">Gaub</option>
                    <option value="Gines Interior">Gines Interior</option>
                    <option value="Gines Patag">Gines Patag</option>
                    <option value="Guibuangan Tigbauan">Guibuangan Tigbauan</option>
                    <option value="Inabasan">Inabasan</option>
                    <option value="Inaca">Inaca</option>
                    <option value="Inaladan">Inaladan</option>
                    <option value="Ingas">Ingas</option>
                    <option value="Ito Norte">Ito Norte</option>
                    <option value="Ito Sur">Ito Sur</option>
                    <option value="Janipaan Central">Janipaan Central</option>
                    <option value="Janipaan Este">Janipaan Este</option>
                    <option value="Janipaan Oeste">Janipaan Oeste</option>
                    <option value="Janipaan Olo">Janipaan Olo</option>
                    <option value="Jelicuon Lusaya">Jelicuon Lusaya</option>
                    <option value="Jelicuon Montinola">Jelicuon Montinola</option>
                    <option value="Lag-an">Lag-an</option>
                    <option value="Leong">Leong</option>
                    <option value="Lutac">Lutac</option>
                    <option value="Manguna">Manguna</option>
                    <option value="Maraguit">Maraguit</option>
                    <option value="Morubuan">Morubuan</option>
                    <option value="Pacatin">Pacatin</option>
                    <option value="Pagotpot">Pagotpot</option>
                    <option value="Pamul-ogan">Pamul-ogan</option>
                    <option value="Pamuringao Proper">Pamuringao Proper</option>
                    <option value="Pamuringao Garrido">Pamuringao Garrido</option>
                    <option value="Zone I Pob. (Barangay 1)">Zone I Pob. (Barangay 1)</option>
                    <option value="Zone II Pob. (Barangay 2)">Zone II Pob. (Barangay 2)</option>
                    <option value="Zone III Pob. (Barangay 3)">Zone III Pob. (Barangay 3)</option>
                    <option value="Zone IV Pob. (Barangay 4)">Zone IV Pob. (Barangay 4)</option>
                    <option value="Zone V Pob. (Barangay 5)">Zone V Pob. (Barangay 5)</option>
                    <option value="Zone VI Pob. (Barangay 6)">Zone VI Pob. (Barangay 6)</option>
                    <option value="Zone VII Pob. (Barangay 7)">Zone VII Pob. (Barangay 7)</option>
                    <option value="Zone VIII Pob. (Barangay 8)">Zone VIII Pob. (Barangay 8)</option>
                    <option value="Zone IX Pob. (Barangay 9)">Zone IX Pob. (Barangay 9)</option>
                    <option value="Zone X Pob. (Barangay 10)">Zone X Pob. (Barangay 10)</option>
                    <option value="Zone XI Pob. (Barangay 11)">Zone XI Pob. (Barangay 11)</option>
                    <option value="Pungtod">Pungtod</option>
                    <option value="Puyas">Puyas</option>
                    <option value="Salacay">Salacay</option>
                    <option value="Sulanga">Sulanga</option>
                    <option value="Tabucan">Tabucan</option>
                    <option value="Tacdangan">Tacdangan</option>
                    <option value="Talanghauan">Talanghauan</option>
                    <option value="Tigbauan Road">Tigbauan Road</option>
                    <option value="Tinio-an">Tinio-an</option>
                    <option value="Tiring">Tiring</option>
                    <option value="Tupol Central">Tupol Central</option>
                    <option value="Tupol Este">Tupol Este</option>
                    <option value="Tupol Oeste">Tupol Oeste</option>
                    <option value="Tuy-an">Tuy-an</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="contactNo" class="form-label">Contact Number: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="contactNo" id="contactNo" maxlength="11" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email: <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
    <label for="password" class="form-label">Password: <span class="text-danger">*</span></label>
    <div class="input-group">
        <input type="password" class="form-control" name="password" id="password" required oninput="handlePasswordVerification()">
        <span class="input-group-text" id="passwordStatusIcon"></span>
    </div>
</div>

<div class="mb-3">
    <label for="confirmPassword" class="form-label">Confirm Password: <span class="text-danger">*</span></label>
    <div class="input-group">
        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" required oninput="handlePasswordVerification()">
        <span class="input-group-text" id="confirmPasswordStatusIcon"></span>
    </div>
</div>

 <div id="map"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var longitude = 122.48181;
    var latitude = 10.87993;

    // Initialize text elements
    var latTextElement = document.getElementById('latText');
    var lngTextElement = document.getElementById('lngText');

    var map = L.map('map').setView([10.87993, 122.48181], 18);

    L.tileLayer('https://api.maptiler.com/maps/dataviz/{z}/{x}/{y}@2x.png?key=A8yOIIILOal2yE0Rvb63', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
        }).addTo(map);

    var marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);

    // Update lat, lng, and text elements on marker drag
    marker.on('dragend', function(e) {
        var latLng = e.target.getLatLng();
        var lat = latLng.lat.toFixed(6);
        var lng = latLng.lng.toFixed(6);

        // Set values in hidden form fields
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;


        // Update text elements
        latTextElement.innerText = 'Latitude: ' + lat;
        lngTextElement.innerText = 'Longitude: ' + lng;
    });
});
</script>

            <!-- Add hidden fields to store latitude and longitude -->
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="notifType" id="notifType" value="0" >
            <input type="hidden" name="notifDate" id="notifDate" value="<?php echo date('Y-m-d'); ?>">
            <input type="hidden" name="notifMessage" id="notifMessage" value="A new resident had registered." >

            <button type="submit" class="btn btn-primary" id="registerButton" disabled>Register</button>
            <a href="index.php" class="btn btn-primary mt">Home</a>
        </form>
</div>
    <!-- Add Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
