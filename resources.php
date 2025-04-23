<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educational Resources - DropTrace</title>
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
                    <a href="profile.php" class="text-light hover:text-white font-medium">Profile</a>
                    <a href="resources.php" class="text-white font-medium">Resources</a>
                    <a href="logout.php" class="text-light hover:text-white font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold text-center text-primary mb-8">Educational Resources</h1>
        <p class="text-xl text-center text-secondary mb-12">Access study materials, learning resources, and support information</p>

        <!-- Resource Categories -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Study Materials -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-accent text-4xl mb-4">
                    <i class="fas fa-book"></i>
                </div>
                <h2 class="text-2xl font-bold text-primary mb-4">Study Materials</h2>
                <ul class="space-y-3">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">NCERT Textbooks</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Sample Papers</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Practice Worksheets</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Video Lectures</a>
                    </li>
                </ul>
            </div>

            <!-- Learning Support -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-accent text-4xl mb-4">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h2 class="text-2xl font-bold text-primary mb-4">Learning Support</h2>
                <ul class="space-y-3">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Online Tutoring</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Study Groups</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Mentorship Program</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Career Guidance</a>
                    </li>
                </ul>
            </div>

            <!-- Financial Aid -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-accent text-4xl mb-4">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <h2 class="text-2xl font-bold text-primary mb-4">Financial Aid</h2>
                <ul class="space-y-3">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Scholarships</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Education Loans</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Government Schemes</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-accent mr-2"></i>
                        <a href="#" class="text-secondary hover:text-primary">Financial Planning</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Additional Resources -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-12">
            <h2 class="text-2xl font-bold text-primary mb-6">Additional Resources</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-semibold text-secondary mb-4">Online Learning Platforms</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-external-link-alt text-accent mr-2"></i>
                            <a href="https://swayam.gov.in/" target="_blank" class="text-secondary hover:text-primary">SWAYAM</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-external-link-alt text-accent mr-2"></i>
                            <a href="https://diksha.gov.in/" target="_blank" class="text-secondary hover:text-primary">DIKSHA</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-external-link-alt text-accent mr-2"></i>
                            <a href="https://www.khanacademy.org/" target="_blank" class="text-secondary hover:text-primary">Khan Academy</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-secondary mb-4">Support Services</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-phone text-accent mr-2"></i>
                            <span class="text-secondary">Helpline: 1800-123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-accent mr-2"></i>
                            <a href="mailto:support@droptrace.com" class="text-secondary hover:text-primary">support@droptrace.com</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-calendar-alt text-accent mr-2"></i>
                            <span class="text-secondary">24/7 Support Available</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-primary mb-6">Quick Links</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="analysis.php" class="bg-accent hover:bg-tertiary text-white text-center py-3 px-6 rounded-lg transition duration-300">
                    View Analysis
                </a>
                <a href="intervensions.php" class="bg-accent hover:bg-tertiary text-white text-center py-3 px-6 rounded-lg transition duration-300">
                    Interventions
                </a>
                <a href="contact.php" class="bg-accent hover:bg-tertiary text-white text-center py-3 px-6 rounded-lg transition duration-300">
                    Contact Support
                </a>
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