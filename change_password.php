<?php
session_start();

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dropout';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $conn->real_escape_string($_POST['username']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Check if the username exists
    $check_sql = "SELECT id FROM users WHERE username = '$username'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows == 1) {
        // Update the password
        $update_sql = "UPDATE users SET password = '$new_password' WHERE username = '$username'";
        if ($conn->query($update_sql) === TRUE) {
            $success = "Password updated successfully! You can now log in.";
        } else {
            $error = "Error updating password: " . $conn->error;
        }
    } else {
        $error = "Username not found. Please try again.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Dropout Analysis System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #08141B; /* Dark background for the page */
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white shadow-xl rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Change Password</h1>
            <p class="text-gray-600">Reset your password</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php elseif (isset($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <form action="change_password.php" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent"
                    placeholder="Enter your username">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" id="new_password" name="new_password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent"
                    placeholder="Enter your new password">
            </div>

            <button type="submit" 
                    class="w-full bg-accent hover:bg-accent-dark text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md">
                Change Password
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Remembered your password? <a href="login.php" class="font-medium text-accent hover:text-accent-dark">Sign In</a>
            </p>
        </div>
    </div>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#08141B',
                        'secondary': '#11212D',
                        'tertiary': '#233745',
                        'accent': '#4A5C6A',
                        'accent-dark': '#3a4a56',
                        'light': '#9BAAAB',
                        'lighter': '#CCD0CF',
                    }
                }
            }
        }
    </script>
</body>
</html>