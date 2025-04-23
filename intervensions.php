<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intervention Strategies | Dropout Analysis</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
                        'highlight': '#3B82F6' // New vibrant blue for charts
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' }
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .intervention-card {
            transition: all 0.3s ease;
            border-left: 4px solid #4A5C6A;
            animation: fade-in 0.6s ease-out;
        }
        .intervention-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 30px -10px rgba(8, 20, 27, 0.2);
        }
        .stats-card {
            background: linear-gradient(135deg, #11212D 0%, #233745 100%);
            animation: float 4s ease-in-out infinite;
            animation-delay: calc(var(--order) * 0.2s);
        }
        .chart-container {
            background: linear-gradient(to bottom, #F8FAFC, #FFFFFF);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(8, 20, 27, 0.1);
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="bg-lighter font-sans">
    <!-- Navigation -->
    <nav class="bg-primary text-white shadow-lg">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-graduation-cap text-2xl text-accent mr-2"></i>
                <span class="font-semibold text-xl">DropTrace</span>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-light hover:text-white">Home</a>
                <a href="analysis.php" class="text-light hover:text-white">Analysis</a>
                <a href="intervensions.php" class="text-light hover:text-white">Interventions</a>
                <a href="aboutus.php" class="text-light hover:text-white font-medium">About Us</a>
                <a href="pdf_analysis.php" class="text-light hover:text-white font-medium">PDF Analysis</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="text-light hover:text-white">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-accent hover:bg-tertiary text-white px-4 py-2 rounded">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16 animate-fade-in">
            <h1 class="text-4xl font-bold text-primary mb-4">Intervention Strategies</h1>
            <p class="text-xl text-secondary max-w-3xl mx-auto">
                Data-driven approaches to reduce dropout rates with measurable impact
            </p>
        </div>

        <!-- Impact Visualization Section -->
        <div class="chart-container p-6 mb-16 animate-fade-in">
            <h2 class="text-2xl font-bold text-primary mb-6">Intervention Effectiveness Comparison</h2>
            <div style="height: 450px;">
                <canvas id="impactChart"></canvas>
            </div>
        </div>

        <!-- Key Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="stats-card text-white p-6 rounded-lg shadow-md" style="--order: 1;">
                <div class="flex items-center mb-2">
                    <i class="fas fa-rupee-sign text-blue-300 mr-2"></i>
                    <h3 class="text-lg font-semibold">Financial Aid</h3>
                </div>
                <div class="text-3xl font-bold mb-2">85%</div>
                <div class="text-light text-sm">Reduction in economically disadvantaged dropouts</div>
            </div>
            <div class="stats-card text-white p-6 rounded-lg shadow-md" style="--order: 2;">
                <div class="flex items-center mb-2">
                    <i class="fas fa-female text-purple-300 mr-2"></i>
                    <h3 class="text-lg font-semibold">Girls' Programs</h3>
                </div>
                <div class="text-3xl font-bold mb-2">40%</div>
                <div class="text-light text-sm">Higher impact for female students</div>
            </div>
            <div class="stats-card text-white p-6 rounded-lg shadow-md" style="--order: 3;">
                <div class="flex items-center mb-2">
                    <i class="fas fa-chart-line text-green-300 mr-2"></i>
                    <h3 class="text-lg font-semibold">Combined</h3>
                </div>
                <div class="text-3xl font-bold mb-2">2.5x</div>
                <div class="text-light text-sm">Better retention with multi-pronged approaches</div>
            </div>
        </div>

        <!-- Intervention Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <?php
            $interventions = [
                [
                    'icon' => 'rupee-sign',
                    'color' => 'text-blue-400',
                    'bg' => 'bg-blue-100',
                    'title' => 'Financial Support',
                    'desc' => 'Scholarships and conditional cash transfers show 20-30% reduction in dropouts',
                    'effect' => '85%'
                ],
                [
                    'icon' => 'female',
                    'color' => 'text-purple-400',
                    'bg' => 'bg-purple-100',
                    'title' => "Girls' Education",
                    'desc' => 'Sanitary facilities and safety measures reduce female dropout by 40%',
                    'effect' => '78%'
                ],
                [
                    'icon' => 'chalkboard-teacher',
                    'color' => 'text-amber-400',
                    'bg' => 'bg-amber-100',
                    'title' => 'Teacher Training',
                    'desc' => 'Pedagogical training reduces dropout by 15% through better engagement',
                    'effect' => '65%'
                ],
                [
                    'icon' => 'bus',
                    'color' => 'text-red-400',
                    'bg' => 'bg-red-100',
                    'title' => 'Transportation',
                    'desc' => 'School bus programs show 25% improvement in rural attendance',
                    'effect' => '72%'
                ],
                [
                    'icon' => 'utensils',
                    'color' => 'text-green-400',
                    'bg' => 'bg-green-100',
                    'title' => 'Mid-Day Meals',
                    'desc' => 'Nutritional support increases daily attendance by 30%',
                    'effect' => '80%'
                ],
                [
                    'icon' => 'laptop',
                    'color' => 'text-indigo-400',
                    'bg' => 'bg-indigo-100',
                    'title' => 'Digital Learning',
                    'desc' => 'Hybrid learning models show 15% lower dropout in pilots',
                    'effect' => '60%'
                ]
            ];
            
            foreach ($interventions as $intervention): ?>
            <div class="intervention-card bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="<?= $intervention['bg'] ?> p-3 rounded-full mr-4">
                        <i class="fas fa-<?= $intervention['icon'] ?> <?= $intervention['color'] ?> text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-primary"><?= $intervention['title'] ?></h3>
                </div>
                <p class="text-gray-700 mb-4"><?= $intervention['desc'] ?></p>
                <div class="flex items-center text-sm text-accent font-medium">
                    <i class="fas fa-chart-line mr-2"></i>
                    <span>Effectiveness: <?= $intervention['effect'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Enhanced Impact Timeline -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-12">
            <h2 class="text-2xl font-bold text-primary mb-6">Projected Impact Timeline</h2>
            <div class="chart-container p-6" style="height: 400px;">
                <canvas id="timelineChart"></canvas>
            </div>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-primary mb-2">Key Milestones</h3>
                    <ul class="space-y-2 text-secondary">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-accent mt-1 mr-2"></i>
                            <span>3 months: Initial participation boost</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-accent mt-1 mr-2"></i>
                            <span>6 months: Measurable attendance improvement</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-accent mt-1 mr-2"></i>
                            <span>1 year: Significant dropout reduction</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-primary mb-2">Best Practices</h3>
                    <ul class="space-y-2 text-secondary">
                        <li class="flex items-start">
                            <i class="fas fa-lightbulb text-accent mt-1 mr-2"></i>
                            <span>Combine 2+ interventions for synergistic effects</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-lightbulb text-accent mt-1 mr-2"></i>
                            <span>Tailor approaches to local needs</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-lightbulb text-accent mt-1 mr-2"></i>
                            <span>Engage community stakeholders</span>
                        </li>
                    </ul>
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

    <script>
        // Impact Comparison Chart
        const impactCtx = document.getElementById('impactChart').getContext('2d');
        new Chart(impactCtx, {
            type: 'bar',
            data: {
                labels: ['Financial Aid', "Girls' Programs", 'Teacher Training', 'Transportation', 'Mid-Day Meals', 'Digital Learning'],
                datasets: [{
                    label: 'Reduction in Dropout Rates (%)',
                    data: [85, 78, 65, 30, 80, 50],
                    backgroundColor: [
                        'rgba(8, 20, 43, 0.9)',    // Darkest blue (highest impact)
                    'rgba(17, 43, 74, 0.9)',
                    'rgba(35, 85, 125, 0.9)',
                    'rgba(52, 117, 166, 0.9)',
                    'rgba(74, 132, 186, 0.9)',
                    'rgba(120, 170, 210, 0.9)' 
                    ],
                    borderColor: [
                        '#08142B',
                        '#112B4A',
                        '#23557D',
                        '#3475A6',
                        '#4A84BA',
                        '#78AAD2'

                    ],
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Intervention Effectiveness Comparison',
                        font: {
                            size: 18,
                            family: 'Space Grotesk',
                            weight: '600'
                        },
                        padding: {
                            bottom: 20
                        }
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#11212D',
                        titleFont: {
                            family: 'Space Grotesk',
                            size: 14
                        },
                        bodyFont: {
                            family: 'Space Grotesk',
                            size: 12
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(204, 208, 207, 0.2)'
                        },
                        ticks: {
                            font: {
                                family: 'Space Grotesk'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Reduction Percentage',
                            font: {
                                family: 'Space Grotesk',
                                weight: '600'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Space Grotesk',
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });

        // Timeline Chart
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: ['Baseline', '3 Months', '6 Months', '9 Months', '1 Year', '2 Years'],
                datasets: [
                    {
                        label: 'Dropout Rate',
                        data: [100, 85, 70, 60, 45, 30],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#3B82F6',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Target',
                        data: [100, 90, 75, 65, 50, 35],
                        borderColor: '#4A5C6A',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.3,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Space Grotesk',
                                size: 13
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#11212D',
                        titleFont: {
                            family: 'Space Grotesk',
                            size: 14
                        },
                        bodyFont: {
                            family: 'Space Grotesk',
                            size: 12
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 100,
                        grid: {
                            color: 'rgba(204, 208, 207, 0.2)'
                        },
                        ticks: {
                            font: {
                                family: 'Space Grotesk'
                            },
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Dropout Rate',
                            font: {
                                family: 'Space Grotesk',
                                weight: '600'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(204, 208, 207, 0.1)'
                        },
                        ticks: {
                            font: {
                                family: 'Space Grotesk',
                                weight: '500'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Implementation Period',
                            font: {
                                family: 'Space Grotesk',
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>