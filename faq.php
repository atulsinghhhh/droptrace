<?php
// Initialize session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Student Dropout Analysis System</title>
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
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-8px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .hero-bg {
            background-image: linear-gradient(rgba(8, 20, 27, 0.8), rgba(8, 20, 27, 0.8)),
                              url('https://images.unsplash.com/flagged/photo-1574097656146-0b43b7660cb6?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
        }
        .faq-card {
            transition: all 0.3s ease;
        }
        .faq-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-lighter font-sans bg-primary text-lighter font-space">
    <!-- Navigation Bar -->
    <nav class="bg-primary text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-graduation-cap text-2xl text-accent mr-2"></i>
                    <span class="font-semibold text-xl tracking-tight">DropTrace</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-light hover:text-white font-medium">Home</a>
                    <a href="analysis.php" class="text-light hover:text-white font-medium">Analysis</a>
                    <a href="intervensions.php" class="text-light hover:text-white font-medium">Interventions</a>
                    <a href="aboutus.php" class="text-light hover:text-white font-medium">About Us</a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="text-light hover:text-white">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="bg-accent hover:bg-tertiary text-white px-4 py-2 rounded">Login</a>
                    <?php endif; ?>
                </div>
                <button class="md:hidden text-white focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg text-white py-24">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 tracking-tight">Frequently Asked Questions</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto text-light leading-relaxed">
                Find answers to common questions about our Student Dropout Analysis System.
            </p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-secondary text-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4 tracking-tight">Your Questions, Answered</h2>
                <div class="w-24 h-1 bg-accent mx-auto"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="faq-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">What is the Student Dropout Analysis System?</h3>
                    <p class="text-light text-sm leading-relaxed">
                        Our system is a data-driven platform designed to identify students at risk of dropping out by analyzing academic, socio-economic, and behavioral factors. It provides insights and recommends interventions to improve retention rates.
                    </p>
                </div>
                <div class="faq-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">Who can use this system?</h3>
                    <p class="text-light text-sm leading-relaxed">
                        The system is designed for educators, school administrators, policymakers, and researchers interested in understanding and reducing student dropout rates. Registered users can access detailed analytics and intervention tools.
                    </p>
                </div>
                <div class="faq-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">How is the data sourced?</h3>
                    <p class="text-light text-sm leading-relaxed">
                        Data is sourced from publicly available educational datasets, government reports, and institutional records. We ensure compliance with data privacy regulations and use anonymized data for analysis.
                    </p>
                </div>
                <div class="faq-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">What kind of interventions are suggested?</h3>
                    <p class="text-light text-sm leading-relaxed">
                        Interventions include targeted financial aid, academic counseling, mentorship programs, and flexible learning options. Recommendations are based on identified risk factors and tailored to individual student needs.
                    </p>
                </div>
                <div class="faq-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">Is the system free to use?</h3>
                    <p class="text-light text-sm leading-relaxed">
                        Basic access to the system is free, including general statistics and trends. Advanced features, such as detailed analytics and intervention planning, may require a subscription or institutional partnership.
                    </p>
                </div>
                <div class="faq-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">How can I contribute to the project?</h3>
                    <p class="text-light text-sm leading-relaxed">
                        You can contribute by sharing data, providing feedback, or collaborating on research. Visit our <a href="https://github.com/saiteja2108/DROPOUT-ANALYSIS" class="text-accent hover:underline" target="_blank">GitHub repository</a> to get involved.
                    </p>
                </div>
                <div class="faq-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">How accurate are the dropout risk predictions?</h3>
                    <p class="text-light text-sm leading-relaxed">
                        Our predictive models are built using machine learning techniques and validated against historical data, achieving high accuracy. However, predictions are probabilistic and should be used as a guide alongside professional judgment.
                    </p>
                </div>
                <div class="faq-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">How can I contact the team?</h3>
                    <p class="text-light text-sm leading-relaxed">
                        Reach us via email at <a href="mailto:dropoutanalysis@gmail.com" class="text-accent hover:underline">dropoutanalysis@gmail.com</a> or call +91 7836912212. You can also visit us at Lovely Professional University, Phagwara, Punjab.
                    </p>
                </div>
            </div>
        </div>
    </section>
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
                        <li><a href="#" class="text-light hover:text-white font-medium">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 tracking-tight">Contact Us</h3>
                    <ul class="space-y-2 text-light">
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-2"></i>
                            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dropoutanalysisofficial@gmail.com" target="_blank" class="text-light hover:text-white">dropoutanalysisofficial@gmail.com</a>
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
