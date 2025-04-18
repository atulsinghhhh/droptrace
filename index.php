<?php
// Initialize session
session_start();

// Database connection for dynamic stats
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dropout_analysis';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch statistics
    $stmt = $conn->query("SELECT 
        (SELECT COUNT(*) FROM students) AS total_students,
        (SELECT COUNT(*) FROM students WHERE dropout_risk = 'high') AS at_risk,
        (SELECT COUNT(*) FROM interventions) AS interventions");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Use default stats if DB connection fails
    $stats = [
        'total_students' => 2400,
        'at_risk' => 4600,
        'interventions' => 300
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dropout Analysis System</title>
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
            background-image: linear-gradient(rgba(18, 38, 49, 0.8), rgba(8, 20, 27, 0.8)), 
                              url('https://images.unsplash.com/flagged/photo-1574097656146-0b43b7660cb6?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-lighter font-sans bg-primary text-lighter font-space">
    <!-- Navigation Bar -->
    <nav class="bg-primary text-white shadow-lg fixed top-0 left-0 w-full z-50">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo Section -->
            <div class="flex items-center">
                <i class="fas fa-graduation-cap text-2xl text-accent mr-2"></i>
                <span class="font-semibold text-xl tracking-tight">DropTrace</span>
            </div>

            <!-- Navigation Links and Buttons -->
            <div class="flex items-center space-x-6">
                <a href="index.php" class="text-light hover:text-white font-medium">Home</a>
                <a href="analysis.php" class="text-light hover:text-white font-medium">Analysis</a>
                <a href="intervensions.php" class="text-light hover:text-white font-medium">Interventions</a>
                <a href="aboutus.php" class="text-light hover:text-white font-medium">About Us</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="text-light hover:text-white font-medium">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-accent hover:bg-tertiary text-white px-4 py-2 rounded">Login</a>
                <?php endif; ?>
                <!-- Dark Mode Toggle Button -->
                <button id="theme-toggle" class="text-white focus:outline-none">
                    <i id="theme-icon" class="fas fa-sun"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

<script>
    // Theme toggle logic
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    // Check for saved theme in localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        body.classList.add(savedTheme);
        themeIcon.className = savedTheme === 'dark-mode' ? 'fas fa-moon' : 'fas fa-sun';
    }

    themeToggle.addEventListener('click', () => {
        if (body.classList.contains('dark-mode')) {
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light-mode');
            themeIcon.className = 'fas fa-sun';
        } else {
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark-mode');
            themeIcon.className = 'fas fa-moon';
        }
    });
</script>

<style>
    /* Light Mode (Default) */
    body {
        background-color: #f9fafb;
        color: #1a202c;
    }

    /* Dark Mode */
    body.dark-mode {
        background-color: #1a202c;
        color: #f9fafb;
    }

    body.dark-mode .bg-primary {
        background-color: #2d3748;
    }

    body.dark-mode .bg-secondary {
        background-color: #4a5568;
    }

    body.dark-mode .bg-tertiary {
        background-color: #2d3748;
    }

    body.dark-mode .text-light {
        color: #e2e8f0;
    }

    body.dark-mode .text-lighter {
        color: #cbd5e0;
    }

    body.dark-mode a:hover {
        color: #63b3ed;
    }
</style>
    <!-- Hero Section -->
    <section class="hero-bg text-white py-24">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 tracking-tight">Turning Dropouts into Comebacks</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto text-light leading-relaxed">
                "Dropouts aren't numbers; they're stories unwritten, talents untapped, and hopes unfulfilled."
            </p>
            <a href="analysis.php" class="bg-accent hover:bg-tertiary text-white font-bold py-3 px-8 rounded-lg inline-block transition duration-300 tracking-wide">
                EXPLORE DATA
            </a>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="analysis" class="py-16 bg-secondary text-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4 tracking-tight">Retention Rate Over Time</h2>
                <div class="w-24 h-1 bg-accent mx-auto"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="stat-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">Primary (Grades 1-5)</h3>
                    <p class="text-light text-sm leading-relaxed">
                        <span class="text-accent">87.0% → 95.4% → 85.4%</span> (2019-20 to 2023-24)
                    </p>
                </div>
                <div class="stat-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">Elementary (Grades 6-8)</h3>
                    <p class="text-light text-sm leading-relaxed">
                        <span class="text-accent">74.6% → 81.2% → 78.0%</span> (2019-20 to 2023-24)
                    </p>
                </div>
                <div class="stat-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">Secondary (Grades 9-10)</h3>
                    <p class="text-light text-sm leading-relaxed">
                        <span class="text-accent">59.6% → 65.5% → 63.8%</span> (2019-20 to 2023-24)
                    </p>
                </div>
                <div class="stat-card bg-tertiary p-6 rounded-lg">
                    <h3 class="text-xl font-bold mb-3 tracking-tight">Higher Secondary (11-12)</h3>
                    <p class="text-light text-sm leading-relaxed">
                        <span class="text-accent">40.2% → 45.6%</span> (2019-20 to 2023-24)
                    </p>
                </div>
            </div>
            <p class="text-light text-center max-w-4xl mx-auto leading-relaxed">
                Student retention rates in India's education system reflect various institutional challenges. 
                Before analyzing trends, let's examine the core concepts.
            </p>
        </div>
    </section>

    <!-- Data Visualization Section -->
    <div class="relative overflow-hidden min-h-[60vh] flex items-center">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-accent/10 to-transparent opacity-20"></div>
        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <h1 class="text-6xl md:text-8xl font-bold mb-6 text-accent">Dropout Analysis</h1>
            <p class="text-xl md:text-2xl text-light max-w-2xl">
                Visualizing patterns in student retention and risk factors
            </p>
        </div>
    </div>

    <!-- Data Visualization Section -->
    <section class="py-16">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center mb-16">
                <div class="md:w-1/2 mb-8 md:mb-0 md:pr-8">
                    <h2 class="text-3xl font-bold mb-6 text-light">Dropout Rate Trends (2015-2025)</h2>
                    <p class="text-lighter mb-4">
                        Tracking national dropout rates over time reveals key patterns in educational disengagement. Our system leverages historical data and predictive modeling to identify at-risk students and pinpoint critical intervention periods.
                    </p>
                    <p class="text-lighter mb-4">
                        By analyzing socio-economic factors, academic performance, and school infrastructure, we provide data-driven insights to help institutions implement targeted solutions—financial aid, counseling, and flexible learning options.
                    </p>
                    <p class="text-lighter">
                        With a strategic, evidence-based approach, we aim to reduce dropout rates and build a resilient, inclusive education system where every student thrives.
                    </p>
                </div>
                <div class="md:w-1/2 animate-float">
                    <div class="bg-secondary p-6 rounded-xl border border-tertiary">
                        <canvas id="dropoutChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row-reverse items-center">
                <div class="md:w-1/2 mb-8 md:mb-0 md:pl-8">
                    <h2 class="text-3xl font-bold mb-6 text-accent">Key Risk Factors</h2>
                    <ul class="space-y-4 text-lighter">
                        <li class="flex items-start">
                            <i class="fas fa-chart-line text-light mt-1 mr-3"></i>
                            <span>Academic difficulties and poor performance</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-money-bill-wave text-light mt-1 mr-3"></i>
                            <span>Economic hardship and need to work</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-home text-light mt-1 mr-3"></i>
                            <span>Lack of family support or engagement</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-users text-light mt-1 mr-3"></i>
                            <span>Social and behavioral issues</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-chalkboard-teacher text-light mt-1 mr-3"></i>
                            <span>School climate and teacher relationships</span>
                        </li>
                    </ul>
                </div>
                <div class="md:w-1/2 animate-float">
                    <div class="bg-secondary p-6 rounded-xl border border-tertiary">
                        <canvas id="factorsChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Corrected Dropout Rate Chart with 2015-2025 data
        const dropoutCtx = document.getElementById('dropoutChart').getContext('2d');
        const dropoutChart = new Chart(dropoutCtx, {
            type: 'line',
            data: {
                labels: ['2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'],
                datasets: [{
                    label: 'National Dropout Rate %',
                    data: [5.2, 5.0, 5.1, 5.4, 5.7, 6.0, 6.2, 6.3, 6.1, 5.8, 5.5],
                    borderColor: '#9BAAAB',
                    backgroundColor: 'rgba(74, 92, 106, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#CCD0CF',
                    pointBorderColor: '#4A5C6A',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#4A5C6A',
                            font: {
                                family: 'Space Grotesk',
                                weight: '600'
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: '#11212D',
                        titleColor: '#CCD0CF',
                        bodyColor: '#9BAAAB',
                        borderColor: '#4A5C6A',
                        borderWidth: 1,
                        padding: 12,
                        titleFont: {
                            family: 'Space Grotesk',
                            weight: '600',
                            size: 14
                        },
                        bodyFont: {
                            family: 'Space Grotesk',
                            size: 12
                        },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 4.5,
                        max: 6.5,
                        grid: {
                            color: '#233745',
                            borderDash: [3, 3]
                        },
                        ticks: {
                            color: '#4A5C6A',
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        border: {
                            dash: [3, 3]
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#4A5C6A'
                        },
                        border: {
                            dash: [3, 3]
                        }
                    }
                }
            }
        });

        // Risk Factors Chart
        const factorsCtx = document.getElementById('factorsChart').getContext('2d');
        const factorsChart = new Chart(factorsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Academic', 'Economic', 'Family', 'Social', 'School'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: [
                        '#08141B',
                        '#CCD0CF',
                        '#233745',
                        '#4A5C6A',
                        '#9BAAAB'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#4A5C6A',
                            padding: 20,
                            boxWidth: 15,
                            font: {
                                family: 'Space Grotesk',
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#11212D',
                        titleColor: '#CCD0CF',
                        bodyColor: '#9BAAAB',
                        borderColor: '#4A5C6A',
                        borderWidth: 1,
                        padding: 12,
                        titleFont: {
                            family: 'Space Grotesk',
                            weight: '600',
                            size: 14
                        },
                        bodyFont: {
                            family: 'Space Grotesk',
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    </script>
</body>
</html>