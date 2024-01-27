<?php
include_once "../class/resident.php";

$resident = new Resident();

if (isset($_POST['brgyid'])) {
    $brgyID = $_POST['brgyid'];

    $officers = $resident->getOfficersByBarangay($brgyID);

    if ($officers && $officers->num_rows > 0) {
        while ($row = $officers->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td>
                <form method="post" action="process_revoke.php" onsubmit="return confirmRevoke();">
                    <input type="hidden" name="residentID" value="' . $row['residentID'] . '">
                    <input type="hidden" name="brgyID" value="' . $brgyID . '">
                    <button type="submit" name="Revoke" class="btn btn-danger">Revoke</button>
                </form>
            </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No officers found.</td></tr>';
    }
} else {
    echo '<tr><td colspan="3">Invalid request.</td></tr>';
}
?>

<script>
    function confirmRevoke() {
        return confirm('Are you sure you want to revoke this officer?');
    }
</script>
