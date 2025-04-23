<?php
session_start();

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dropout_analysis';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = 'user'; // Default role for new users

    // Validate password
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Username or email already exists. Please try again.";
            } else {
                // Insert new user into the database with hashed password
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $email, $role]);
                
                // Create empty profile for the new user
                $user_id = $conn->lastInsertId();
                $stmt = $conn->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
                $stmt->execute([$user_id]);
                
                // Set success message
                $success = "User registered successfully! You can now login.";
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Dropout Analysis System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-school {
            background-image: linear-gradient(rgba(26, 57, 77, 0.9), rgba(11, 38, 54, 0.9)), 
                              url('https://media.istockphoto.com/id/1163985429/photo/group-of-schoolboys-and-schoolgirls-at-school-campus.jpg?s=1024x1024&w=is&k=20&c=PFTE79VmLQhc9JKFYZT5Ji_flbqFqL91BeDdVHXyBYc=');
            background-size: cover;
            background-position: center;
        }
        .feature-card {
            transition: all 0.3s ease;
            background-color: rgba(35, 55, 69, 0.7); /* Darker background for cards */
        }
        .feature-card:hover {
            transform: translateY(-5px);
            background-color: rgba(35, 55, 69, 0.9); /* Even darker on hover */
        }
        body {
            background-color: #08141B; /* Dark background for the page */
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-6xl bg-white shadow-xl rounded-lg overflow-hidden flex">
        <!-- Left side - Image and Features -->
        <div class="w-1/2 bg-school hidden md:block">
            <div class="h-full flex flex-col justify-between p-8 text-white">
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-2">Dropout Analysis System</h2>
                    <p class="text-blue-200">Join us to identify and address student dropout challenges</p>
                </div>
                
                <div class="space-y-4">
                    <div class="feature-card p-4 rounded-lg backdrop-blur-sm">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center mr-4">
                                <i class="fas fa-chart-pie text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Comprehensive Analytics</h4>
                                <p class="text-sm text-blue-100">Detailed insights into student performance</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card p-4 rounded-lg backdrop-blur-sm">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center mr-4">
                                <i class="fas fa-bell text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Early Warning System</h4>
                                <p class="text-sm text-blue-100">Identify at-risk students proactively</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card p-4 rounded-lg backdrop-blur-sm">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center mr-4">
                                <i class="fas fa-hands-helping text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Intervention Tracking</h4>
                                <p class="text-sm text-blue-100">Monitor effectiveness of support programs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right side - Registration Form -->
        <div class="w-full md:w-1/2 p-10 bg-gray-50"> <!-- Light gray background for form area -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Create an Account</h1>
                <p class="text-gray-600">Join the Dropout Analysis System</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>
            
            <form action="register.php" method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="username" name="username" required
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent"
                            placeholder="Enter your username">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent"
                            placeholder="Enter your email">
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent"
                            placeholder="Enter your password">
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-accent hover:bg-accent-dark text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md">
                    Create Account
                </button>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-500">
                    Already have an account? <a href="login.php" class="font-medium text-accent hover:text-accent-dark">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="absolute bottom-0 w-full py-4 text-center text-xs text-gray-400 bg-primary">
        <p>© <?php echo date('Y'); ?> Dropout Analysis System | For Educational Use Only</p>
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