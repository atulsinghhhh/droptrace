<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Dropout Analysis System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #11212D; /* Dark background */
        }
        .team-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: linear-gradient(135deg, #233745, #4A5C6A); /* Gradient for team cards */
            border: 1px solid #4A5C6A; /* Border color */
        }
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .section-title {
            color: #CCD0CF; /* Title color */
        }
        .contact-card {
            background: linear-gradient(135deg, #233745, #4A5C6A); /* Gradient for contact card */
            border: 1px solid #4A5C6A; /* Border color */
        }
        .text-body {
            color: #9BAAAB; /* Body text color */
        }
        .vertical-section {
            margin-bottom: 2rem; /* Space between sections */
        }
    </style>
</head>
<body class="font-sans">
    <!-- Navigation -->
    <nav class="bg-primary text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                 <div class="flex items-center">
                <i class="fas fa-graduation-cap text-2xl text-accent mr-2"></i>
                <span class="font-semibold text-xl tracking-tight">DropTrace</span>
            </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-light hover:text-white">Home</a>
                    <a href="analysis.php" class="text-light hover:text-white">Analysis</a>
                    <a href="intervensions.php" class="text-light hover:text-white">Interventions</a>
                    <a href="aboutus.php" class="text-light hover:text-white">About Us</a>
                    <a href="pdf_analysis.php" class="text-light hover:text-white font-medium">PDF Analysis</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="bg-accent hover:bg-tertiary text-white px-4 py-2 rounded">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

   <!-- About Us Section -->
   <section class="container mx-auto px-6 py-12">
    <div class="text-center mb-5 animate-fade-in-up">
        <h1 class="text-4xl font-bold mb-4 transform transition-all duration-500 hover:scale-105">
            <span class="text-white">
                About Our Initiative
            </span>
        </h1>
        <p class="text-lg text-body animate-fade-in delay-100">
            Pioneering Educational Insights Through Data-Driven Solutions
        </p>
    </div>
</section>
        <div class="mt-6 flex justify-end">
            <div class="w-24 h-1 bg-accent animate-pulse"></div>
        </div>
    </div>
</section>

       <!-- Team Section -->
<div class="space-y-12">
   <!-- Team Member 1 - Left Aligned -->
<div class="flex flex-col md:flex-row items-center gap-8">
    <div class="w-full bg-tertiary rounded-lg shadow-md p-8">
        <div class="team-card rounded-lg shadow-md p-8 text-center">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="md:w-1/3">
                    <img src="priya.jpg" alt="Priya Pandey" class="w-100 h-60 mx-auto mb-4 rounded-full">
                </div>
                <div class="md:w-2/3 text-left">
                    <h3 class="text-2xl font-bold text-white mb-2">Priya Pandey</h3>
                    <p class="text-body text-lg mb-4">Team Leader</p>
                    <p class="text-body mb-4">
                    Visionary & UI/UX Designer
                    The mind behind the concept, layout, and structure of the entire platform. From ideation to design, every visual and functional flow was meticulously crafted to ensure clarity, usability, and a compelling user experience.
                    </p>
                    <div class="flex justify-start space-x-4">
    <a href="https://www.linkedin.com/in/pryfi404/" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-600">
        <i class="fab fa-linkedin fa-lg"></i>
    </a>
    <a href="https://github.com/PryFi-404" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-600">
        <i class="fab fa-github fa-lg"></i>
    </a>
</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Member 2 - Right Aligned -->
<div class="flex flex-col md:flex-row items-center gap-8">
    <div class="w-full bg-tertiary rounded-lg shadow-md p-8">
        <div class="team-card rounded-lg shadow-md p-8 text-center">
            <div class="flex flex-col md:flex-row-reverse items-center gap-8">
                <div class="md:w-1/3">
                <img src="aastha.jpeg" alt="Aastha Ghosh" class="w-90 h-80 mx-auto mb-4 rounded-full">
                </div>
                <div class="md:w-2/3 text-left">
                    <h3 class="text-2xl font-bold text-white mb-2">Aastha Ghosh</h3>
                    <p class="text-body text-lg mb-4">Team member</p>
                    <p class="text-body mb-4">
                    Backend Developer & Intervention Architect
                    Brought the vision to life through a robust backend framework. She also designed and integrated data-driven interventions, turning analytics into meaningful action to address student dropouts.
                    </p>
<div class="flex justify-start space-x-4">
    <a href="https://www.linkedin.com/in/aasthaghosh24/" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-600">
        <i class="fab fa-linkedin fa-lg"></i>
    </a>
    <a href="https://github.com/aasthaghosh" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-600">
        <i class="fab fa-github fa-lg"></i>
    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Member 3 - Left Aligned -->
<div class="flex flex-col md:flex-row items-center gap-8">
    <div class="w-full bg-tertiary rounded-lg shadow-md p-8">
        <div class="team-card rounded-lg shadow-md p-8 text-center">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="md:w-1/3">
                    <img src="yukta.jpg" alt="Alice Johnson" class="w-100 h-80 mx-auto mb-4 rounded-full">
                </div>
                <div class="md:w-2/3 text-left">
                    <h3 class="text-2xl font-bold text-white mb-2">Yukta Shree</h3>
                    <p class="text-body text-lg mb-4">Team member</p>
                    <p class="text-body mb-4">
                    Development Collaborator & Creative Contributor
                    Actively supported both frontend and backend development. With a keen eye for detail and a collaborative spirit, they helped tie everything together, ensuring smooth functionality and consistency throughout the platform.
                    </p>
                    <div class="flex justify-start space-x-4">
    <a href="https://www.linkedin.com/in/yukta-shree-3b2398277/" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-600">
        <i class="fab fa-linkedin fa-lg"></i>
    </a>
    <a href="https://github.com/Yukta233" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-600">
        <i class="fab fa-github fa-lg"></i>
    </a>
</div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Footer -->
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
                        <li><a href="#" class="text-light hover:text-white font-medium">Home</a></li>
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
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dropoutanalysisofficial@gmail.com    " target="_blank" class="text-light hover:text-white">dropoutanalysisofficial@gmail.com</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-2"></i>
                            <a href="tel:+917836912212" class="text-light hover:text-white">+91 7836912212</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                            <a href="https://maps.app.goo.gl/WryHGeufZnG1VmNr8" target="_blank">Lovely Professional University<br>Phagwara, Punjab</a>
                            
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-tertiary mt-12 pt-8 text-center text-light">
                <p>&copy; <?= date('Y') ?> Student Dropout Analysis System. All rights reserved.</p>
            </div>
        </div>
    </footer>
    </script>
</body>
</html>