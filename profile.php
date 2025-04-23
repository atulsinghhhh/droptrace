<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dropout_analysis';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header("Location: logout.php");
        exit();
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - DropTrace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    sans: ['Space Grotesk', 'sans-serif'],
                },
                extend: {
                    colors: {
                        'primary': '#08141B',
                        'secondary': '#11212D',
                        'tertiary': '#233745',
                        'accent': '#4A5C6A',
                        'light': '#9BAAAB',
                        'lighter': '#CCD0CF',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-lighter font-sans">
    <!-- Navigation Bar -->
    <nav class="bg-primary text-white shadow-lg fixed top-0 left-0 w-full z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo Section -->
                <div class="flex items-center">
                    <i class="fas fa-graduation-cap text-2xl text-accent mr-2"></i>
                    <span class="font-semibold text-xl tracking-tight">DropTrace</span>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-light hover:text-white font-medium">Home</a>
                    <a href="analysis.php" class="text-light hover:text-white font-medium">Analysis</a>
                    <a href="intervensions.php" class="text-light hover:text-white font-medium">Interventions</a>
                    <a href="aboutus.php" class="text-light hover:text-white font-medium">About Us</a>
                    <a href="contact.php" class="text-light hover:text-white font-medium">Contact</a>
                    <a href="pdf_analysis.php" class="text-light hover:text-white font-medium">PDF Analysis</a>
                    <a href="profile.php" class="text-white font-medium">Profile</a>
                    <a href="logout.php" class="text-light hover:text-white font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-24">
        <div class="max-w-4xl mx-auto">
            <!-- Profile Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="bg-secondary h-32"></div>
                <div class="px-8 py-6">
                    <div class="flex items-center">
                        <div class="w-24 h-24 rounded-full bg-accent flex items-center justify-center text-white text-4xl">
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        </div>
                        <div class="ml-6">
                            <h1 class="text-3xl font-bold text-primary"><?= htmlspecialchars($user['username']) ?></h1>
                            <p class="text-light">Member since <?= date('F Y', strtotime($user['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Account Information -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-primary mb-6">Account Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-light text-sm font-medium mb-1">Username</label>
                                <p class="text-primary font-medium"><?= htmlspecialchars($user['username']) ?></p>
                            </div>
                            <div>
                                <label class="block text-light text-sm font-medium mb-1">Email</label>
                                <p class="text-primary font-medium"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <div>
                                <label class="block text-light text-sm font-medium mb-1">Last Login</label>
                                <p class="text-primary font-medium"><?= isset($user['last_login']) ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Not available' ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-primary mb-6">Quick Actions</h2>
                        <div class="space-y-4">
                            <a href="change_password.php" class="block w-full bg-accent hover:bg-tertiary text-white text-center py-2 px-4 rounded-lg transition duration-300">
                                Change Password
                            </a>
                            <a href="analysis.php" class="block w-full bg-secondary hover:bg-tertiary text-white text-center py-2 px-4 rounded-lg transition duration-300">
                                View Analysis
                            </a>
                            <a href="intervensions.php" class="block w-full bg-secondary hover:bg-tertiary text-white text-center py-2 px-4 rounded-lg transition duration-300">
                                View Interventions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-secondary text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4 tracking-tight">DropTrace</h3>
                    <p class="text-light leading-relaxed">
                        Identifying and supporting at-risk students through data analysis.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 tracking-tight">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-light hover:text-white font-medium">Home</a></li>
                        <li><a href="analysis.php" class="text-light hover:text-white font-medium">Analysis</a></li>
                        <li><a href="intervensions.php" class="text-light hover:text-white font-medium">Interventions</a></li>
                        <li><a href="aboutus.php" class="text-light hover:text-white font-medium">About Us</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 tracking-tight">Resources</h3>
                    <ul class="space-y-2">
                        <li><a href="https://github.com/saiteja2108/DROPOUT-ANALYSIS" class="text-light hover:text-white font-medium" target="_blank">Git Hub</a></li>
                        <li><a href="https://www.kaggle.com/code/jeevabharathis/student-dropout-analysis-for-school-education" target="_blank" class="text-light hover:text-white font-medium">Kaggle</a></li>
                        <li><a href="https://www.data.gov.in/keywords/Dropout" class="text-light hover:text-white font-medium" target="_blank">Government Website</a></li>
                        <li><a href="faq.php" class="text-light hover:text-white font-medium">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 tracking-tight">Contact Us</h3>
                    <ul class="space-y-2 text-light">
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-2"></i>
                            <a href="mailto:dropoutanalysisofficial@gmail.com" class="text-light hover:text-white">dropoutanalysisofficial@gmail.com</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-2"></i>
                            <a href="tel:+917836912212" class="text-light hover:text-white">+91 7836912212</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                            <span>Lovely Professional University<br>Phagwara, Punjab</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-tertiary mt-12 pt-8 text-center text-light">
                <p>&copy; <?= date('Y') ?> Student Dropout Analysis System. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html> 