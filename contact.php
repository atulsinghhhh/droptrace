<?php
// Initialize session
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
    $conn = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if ($conn) {
        try {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = "Thank you for your message! We'll get back to you soon.";
        } catch(PDOException $e) {
            $error = "Sorry, there was an error sending your message. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - DropTrace</title>
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
                <div class="flex items-center">
                    <i class="fas fa-graduation-cap text-2xl text-accent mr-2"></i>
                    <span class="font-semibold text-xl tracking-tight">DropTrace</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-light hover:text-white font-medium">Home</a>
                    <a href="analysis.php" class="text-light hover:text-white font-medium">Analysis</a>
                    <a href="intervensions.php" class="text-light hover:text-white font-medium">Interventions</a>
                    <a href="aboutus.php" class="text-light hover:text-white font-medium">About Us</a>
                    <a href="pdf_analysis.php" class="text-light hover:text-white font-medium">PDF Analysis</a>
                    <a href="contact.php" class="text-white font-medium">Contact</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="text-light hover:text-white font-medium">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="bg-accent hover:bg-tertiary text-white px-4 py-2 rounded">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contact Section -->
    <section class="pt-32 pb-16 bg-primary text-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold mb-4">Contact Us</h1>
                <p class="text-light max-w-2xl mx-auto">Have questions or suggestions? We'd love to hear from you. Fill out the form below and we'll get back to you as soon as possible.</p>
            </div>

            <div class="max-w-3xl mx-auto">
                <?php if (isset($success)): ?>
                    <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Name</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-2 rounded-lg bg-tertiary border border-accent focus:outline-none focus:ring-2 focus:ring-accent">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-2 rounded-lg bg-tertiary border border-accent focus:outline-none focus:ring-2 focus:ring-accent">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium mb-2">Subject</label>
                        <input type="text" id="subject" name="subject" required
                            class="w-full px-4 py-2 rounded-lg bg-tertiary border border-accent focus:outline-none focus:ring-2 focus:ring-accent">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium mb-2">Message</label>
                        <textarea id="message" name="message" rows="6" required
                            class="w-full px-4 py-2 rounded-lg bg-tertiary border border-accent focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-accent hover:bg-tertiary text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-secondary text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p>&copy; <?php echo date('Y'); ?> DropTrace. All rights reserved.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-light hover:text-white">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="text-light hover:text-white">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-light hover:text-white">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html> 