<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #004643;
            color: #001e1d; /* Text color matches button text color */
        }
        .container {
            max-width: 400px;
            margin-top: 50px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #e8e4e6;
        }
        h1 {
            color: #f9bc60;
            text-align: center;
        }
        .form-label {
            font-weight: bold;
            color: #001e1d;
        }
        .btn-primary {
            background-color: #f9bc60;
            border-color: #f9bc60;
            color: #001e1d; /* Button text color */
        }
        .btn-primary:hover {
            background-color: #e16162;
            border-color: #e16162;
            color: #001e1d; /* Button text color on hover */
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        a.btn-primary {
            flex: 1; /* Take up equal space */
            text-align: center; /* Center the text */
            color: #001e1d; /* Button text color for the link */
        }
        a.btn-primary:hover {
            background-color: #e16162;
            border-color: #e16162;
            color: #001e1d; /* Button text color for the link on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="POST" action="process_login.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn btn-primary btn-lg">Login</button>
                <a href="index.php" class="btn btn-primary btn-lg">Home</a>
            </div>
            <div style="text-align: center;">
            <a href="resetpass.php">Reset Password</a>
    </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
