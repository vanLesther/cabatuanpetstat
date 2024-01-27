<?php
include_once "../class/resident.php";
$resident = new Resident();

if (isset($_POST['brgyid'])) {
    $brgyID = $_POST['brgyid'];

    $users = $resident->getAllValidResidentByBarangay($brgyID);

    if ($users && $users->num_rows > 0) {
        while ($row = $users->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            
            echo '<td>';
            if ($row['userType'] == 1) {
                echo '<button type="button" class="btn btn-success" disabled>Assigned</button>';
            } else {
                echo '<form method="POST" action="process_assign.php" class="assign-form" data-brgyid="' . $brgyID . '" onsubmit="return confirmAssign(this);">
                <input type="hidden" name="residentID" value="' . $row['residentID'] . '">
                <input type="hidden" name="brgyID" value="' . $brgyID . '">
                <button type="submit" name="Assign" class="btn btn-success">Assign</button>
            </form>';
            
            }
            echo '</td>';

            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No residents found.</td></tr>';
    }
}
?>
<script>
    function confirmAssign(form) {
        return confirm('Are you sure you want to assign this resident?');
    }

   
</script>
