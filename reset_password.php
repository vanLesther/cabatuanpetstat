<?php
require_once("class/db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body>
    <h2>Reset Password</h2>

    <?php
    global $conn;
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["token"])) {
        $token = $_GET["token"];

        // Check if the token exists in the database
        $query = "SELECT * FROM resident WHERE reset_token = '$token'";
        $result = mysqli_query($conn, $query);  // Assuming you are using mysqli

        if ($result && mysqli_num_rows($result) > 0) {
            // Token is valid, allow the user to reset the password
            ?>

            <form action="reset_password.php?token=<?php echo $token; ?>" method="post">
                <label for="password">New Password:</label>
                <input type="password" name="password" required>
                <button type="submit">Reset Password</button>
            </form>

            <?php
        } else {
            echo "Invalid token or token expired.";
        }
    } else {
        echo "Token not provided.";
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $token = $_GET["token"];

        // Update the user's password and clear the reset token
        $query = "UPDATE resident SET password = '$password', reset_token = NULL WHERE reset_token = '$token'";
        mysqli_query($conn, $query);

        echo "Password reset successful. You can now <a href='login.php'>login</a> with your new password.";
    }
    ?>

</body>
</html>
