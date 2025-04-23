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
    die("Connection failed: " . $e->getMessage());
}

// Function to load real data from a CSV file
function loadRealData($filename) {
    $data = [];
    if (file_exists($filename)) {
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) >= 11) { // Ensure row has enough columns
                    $state = $row[0];
                    $data[$state] = [
                        'dropout_rates' => [
                            'primary' => (float)$row[2],
                            'upper_primary' => (float)$row[3],
                            'secondary' => (float)$row[4],
                        ],
                        'causes' => [
                            ['name' => 'Economic Constraints', 'percentage' => (int)$row[5]],
                            ['name' => 'Lack of Interest', 'percentage' => (int)$row[6]],
                            ['name' => 'Distance to School', 'percentage' => (int)$row[7]],
                            ['name' => 'Gender Inequality', 'percentage' => (int)$row[8]],
                            ['name' => 'Others', 'percentage' => (int)$row[9]]
                        ],
                        'strategies' => [
                            'Financial Assistance',
                            'Community Engagement',
                            'Infrastructure Development',
                            'Curriculum Enhancement',
                            'Safety Measures'
                        ],
                        'image' => 'img/' . strtolower(str_replace(' ', '_', $state)) . '.png',
                        'national_comparison' => [
                            'primary' => 'Varies by state',
                            'upper_primary' => 'Varies by state',
                            'secondary' => 'Varies by state'
                        ]
                    ];
                }
            }
            fclose($handle);
        }
    }
    return $data;
}

// Function to load school type data from a CSV file
function loadSchoolTypeData($filename) {
    $data = [];
    if (file_exists($filename)) {
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) >= 9) { // Ensure row has enough columns
                    $schoolType = $row[0];
                    $data[$schoolType] = [
                        'dropout_rates' => [
                            'primary' => (float)$row[1],
                            'upper_primary' => (float)$row[2],
                            'secondary' => (float)$row[3],
                        ],
                        'causes' => [
                            ['name' => 'Poor Infrastructure', 'percentage' => (int)$row[4]],
                            ['name' => 'Economic Constraints', 'percentage' => (int)$row[5]],
                            ['name' => 'Teacher Quality', 'percentage' => (int)$row[6]],
                            ['name' => 'Others', 'percentage' => (int)$row[7]]
                        ],
                        'strategies' => explode(';', $row[8])
                    ];
                }
            }
            fclose($handle);
        }
    }
    return $data;
}

// Load data with error handling
try {
    $states = loadRealData('DOR.csv');
    $schoolTypeData = loadSchoolTypeData('dataset.csv');
    
    if (empty($states) || empty($schoolTypeData)) {
        throw new Exception("Failed to load data from CSV files");
    }
} catch (Exception $e) {
    // Log the error
    error_log("Error loading data: " . $e->getMessage());
    
    // Use fallback data
    $states = [];
    $schoolTypeData = [];
}

// List of all Indian states and union territories (36 as of 2025)
$allStates = [
    'Andaman and Nicobar Islands', 'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 
    'Chandigarh', 'Chhattisgarh', 'Dadra and Nagar Haveli and Daman and Diu', 'Delhi', 
    'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jammu and Kashmir', 'Jharkhand', 
    'Karnataka', 'Kerala', 'Ladakh', 'Lakshadweep', 'Madhya Pradesh', 'Maharashtra', 
    'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Puducherry', 'Punjab', 
    'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 
    'Uttarakhand', 'West Bengal'
];

// National trends data (same as provided)
$nationalTrends = [
    '1960-61' => ['primary' => 64.9, 'upper_primary' => 78.3, 'secondary' => null],
    '2010-11' => ['primary' => 27.4, 'upper_primary' => 40.8, 'secondary' => 49.2]
    // Add more years if available
];

// Ensure that the $states array is populated with real data
if (empty($states)) {
    // Fallback to synthetic data if real data is not loaded
    foreach ($allStates as $state) {
        $states[$state] = [
            'dropout_rates' => [
                'primary' => round($nationalTrends['2010-11']['primary'] * (0.8 + rand(0, 4)/10), 1),
                'upper_primary' => round($nationalTrends['2010-11']['upper_primary'] * (0.8 + rand(0, 4)/10), 1),
                'secondary' => round($nationalTrends['2010-11']['secondary'] * (0.8 + rand(0, 4)/10), 1),
            ],
            'causes' => [
                ['name' => 'Economic Constraints', 'percentage' => 35],
                ['name' => 'Lack of Interest', 'percentage' => 25],
                ['name' => 'Distance to School', 'percentage' => 20],
                ['name' => 'Gender Inequality', 'percentage' => 15],
                ['name' => 'Others', 'percentage' => 5]
            ],
            'strategies' => [
                'Financial Assistance',
                'Community Engagement',
                'Infrastructure Development',
                'Curriculum Enhancement',
                'Safety Measures'
            ],
            'image' => 'img/placeholder.png', // Replace with actual state map images
            'national_comparison' => [
                'primary' => 'Varies by state',
                'upper_primary' => 'Varies by state',
                'secondary' => 'Varies by state'
            ]
        ];
    }
}


// Gender data (synthetic, replace with real data)
$genderData = [
    'Male' => [
        'dropout_rates' => ['primary' => 25.0, 'upper_primary' => 38.0, 'secondary' => 47.0],
        'causes' => [
            ['name' => 'Economic Constraints', 'percentage' => 40],
            ['name' => 'Lack of Interest', 'percentage' => 30],
            ['name' => 'Employment Pressure', 'percentage' => 20],
            ['name' => 'Others', 'percentage' => 10]
        ],
        'strategies' => ['Scholarships', 'Vocational Training', 'Career Counseling']
    ],
    'Female' => [
        'dropout_rates' => ['primary' => 30.0, 'upper_primary' => 43.0, 'secondary' => 52.0],
        'causes' => [
            ['name' => 'Gender Inequality', 'percentage' => 35],
            ['name' => 'Domestic Responsibilities', 'percentage' => 30],
            ['name' => 'Safety Concerns', 'percentage' => 25],
            ['name' => 'Others', 'percentage' => 10]
        ],
        'strategies' => ['Safety Measures', 'Girls\' Education Programs', 'Community Awareness']
    ],
    'Others' => [
        'dropout_rates' => ['primary' => 28.0, 'upper_primary' => 41.0, 'secondary' => 50.0],
        'causes' => [
            ['name' => 'Social Stigma', 'percentage' => 35],
            ['name' => 'Economic Constraints', 'percentage' => 30],
            ['name' => 'Lack of Support', 'percentage' => 25],
            ['name' => 'Others', 'percentage' => 10]
        ],
        'strategies' => ['Inclusive Policies', 'Counseling', 'Community Support']
    ]
];

// Caste data (synthetic, replace with real data)
$casteData = [
    'General' => [
        'dropout_rates' => ['primary' => 20.0, 'upper_primary' => 30.0, 'secondary' => 40.0],
        'causes' => [
            ['name' => 'Economic Constraints', 'percentage' => 30],
            ['name' => 'Lack of Interest', 'percentage' => 25],
            ['name' => 'Others', 'percentage' => 45]
        ],
        'strategies' => ['Financial Aid', 'Career Guidance', 'Quality Education']
    ],
    'OBC' => [
        'dropout_rates' => ['primary' => 25.0, 'upper_primary' => 35.0, 'secondary' => 45.0],
        'causes' => [
            ['name' => 'Economic Constraints', 'percentage' => 35],
            ['name' => 'Social Barriers', 'percentage' => 30],
            ['name' => 'Others', 'percentage' => 35]
        ],
        'strategies' => ['Scholarships', 'Community Programs', 'Skill Development']
    ],
    'SC' => [
        'dropout_rates' => ['primary' => 30.0, 'upper_primary' => 40.0, 'secondary' => 50.0],
        'causes' => [
            ['name' => 'Economic Constraints', 'percentage' => 40],
            ['name' => 'Discrimination', 'percentage' => 30],
            ['name' => 'Others', 'percentage' => 30]
        ],
        'strategies' => ['Reservations', 'Awareness Campaigns', 'Support Systems']
    ],
    'ST' => [
        'dropout_rates' => ['primary' => 35.0, 'upper_primary' => 45.0, 'secondary' => 55.0],
        'causes' => [
            ['name' => 'Geographic Isolation', 'percentage' => 40],
            ['name' => 'Economic Constraints', 'percentage' => 30],
            ['name' => 'Others', 'percentage' => 30]
        ],
        'strategies' => ['Hostel Facilities', 'Tribal Education Programs', 'Infrastructure']
    ]
];

// Govt/Private schools data (synthetic, replace with real data)
$schoolTypeData = [
    
    'Government' => [
        'dropout_rates' => ['primary' => 30.0, 'upper_primary' => 42.0, 'secondary' => 50.0],
        'causes' => [
            ['name' => 'Poor Infrastructure', 'percentage' => 35],
            ['name' => 'Economic Constraints', 'percentage' => 30],
            ['name' => 'Teacher Quality', 'percentage' => 25],
            ['name' => 'Others', 'percentage' => 10]
        ],
        'strategies' => ['Infrastructure Upgrade', 'Teacher Training', 'Free Resources']
    ],
    'Private' => [
        'dropout_rates' => ['primary' => 15.0, 'upper_primary' => 25.0, 'secondary' => 35.0],
        'causes' => [
            ['name' => 'High Fees', 'percentage' => 40],
            ['name' => 'Economic Constraints', 'percentage' => 30],
            ['name' => 'Others', 'percentage' => 30]
        ],
        'strategies' => ['Fee Subsidies', 'Quality Regulation', 'Community Support']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>India Dropout Analysis | Comprehensive Insights</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.min.js"></script>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .state-card {
            transition: all 0.3s ease;
            background-size: cover;
            background-position: center;
            height: 150px;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
        }
        .state-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(24, 54, 71, 0.9), rgba(29, 64, 83, 0.5));
        }
        .state-card .content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.3rem;
            transform: translateY(100px);
            transition: transform 0.3s ease;
        }
        .state-card:hover .content {
            transform: translateY(0);
        }
        .state-card h3 {
            transition: all 0.3s ease;
        }
        .state-card:hover h3 {
            margin-bottom: -20px;
        }
        .states-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
        }
        .filter-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        .filter-card:hover {
            transform: translateY(-4px);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            overflow-y: auto;
            padding: 2rem;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 1200px;
            margin: 2rem auto;
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2rem;
            color: white;
            cursor: pointer;
        }
        .stats-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .stats-title {
            font-weight: 600;
            color: #11212D;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #4A5C6A;
            padding-bottom: 0.75rem;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            height: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-300 font-sans">
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
                <button class="md:hidden text-white focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12">
        <h1 class="text-4xl font-bold text-center text-primary mb-4">India Dropout Analysis</h1>
        <p class="text-xl text-center text-secondary mb-12">Comprehensive insights across states, gender, caste, and school types</p>
        
        <!-- Search Bar -->
        <div class="max-w-md mx-auto mb-8">
            <div class="relative">
                <input type="text" id="stateSearch" placeholder="Search for a state..." 
                       class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex justify-center mb-12">
            <div class="inline-flex rounded-lg bg-accent p-1">
                <button class="filter-tab px-4 py-2 rounded-md text-white font-medium active" data-tab="states">States</button>
                <button class="filter-tab px-4 py-2 rounded-md text-white font-medium" data-tab="gender">Gender</button>
                <button class="filter-tab px-4 py-2 rounded-md text-white font-medium" data-tab="caste">Caste</button>
                <button class="filter-tab px-4 py-2 rounded-md text-white font-medium" data-tab="schools">Govt/Private Schools</button>
            </div>
        </div>

        <!-- States Section -->
        <div id="states-section" class="filter-section">
    <div class="states-grid mb-16" id="stateCards">
        <?php foreach ($states as $state => $data): ?>
            <div class="state-card w-80 cursor-pointer" 
                 style="background-image: url('<?= $data['image'] ?>'); display: none;"
                 data-state-name="<?= strtolower($state) ?>"
                 onclick="openModal('states', '<?= strtolower(str_replace(' ', '-', $state)) ?>')">
                        <div class="content text-white">
                            <h3 class="text-2xl font-bold mb-8"><?= $state ?></h3>
                            <div class="opacity-0 transition-opacity duration-300 state-card:hover:opacity-100">
                                <p class="mb-2"><strong>Secondary Dropout:</strong> <?= $data['dropout_rates']['secondary'] ?>%</p>
                                <p class="mb-4">Varies by state</p>
                                <button class="inline-block bg-accent hover:bg-tertiary text-white font-medium py-2 px-4 rounded">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
        <div id="noResultsMessage" class="text-center py-8 text-gray-600" style="display: none;">
            No states found matching "<span class="font-semibold"></span>"
        </div>
    </div>
</div>

        <!-- Gender Section -->
        <div id="gender-section" class="filter-section hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <?php foreach ($genderData as $gender => $data): ?>
                    <div class="filter-card cursor-pointer" 
                         onclick="openModal('gender', '<?= strtolower($gender) ?>')">
                        <h3 class="text-xl font-bold text-primary mb-4"><?= $gender ?></h3>
                        <p class="text-secondary mb-2"><strong>Secondary Dropout:</strong> <?= $data['dropout_rates']['secondary'] ?>%</p>
                        <p class="text-gray-600">Click to explore detailed analysis</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Caste Section -->
        <div id="caste-section" class="filter-section hidden">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
                <?php foreach ($casteData as $caste => $data): ?>
                    <div class="filter-card cursor-pointer" 
                         onclick="openModal('caste', '<?= strtolower($caste) ?>')">
                        <h3 class="text-xl font-bold text-primary mb-4"><?= $caste ?></h3>
                        <p class="text-secondary mb-2"><strong>Secondary Dropout:</strong> <?= $data['dropout_rates']['secondary'] ?>%</p>
                        <p class="text-gray-600">Click to explore detailed analysis</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Govt/Private Schools Section -->
<div id="schools-section" class="filter-section hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
        <?php foreach ($schoolTypeData as $type => $data): ?>
            <div class="filter-card cursor-pointer" 
                 onclick="openModal('schools', '<?= strtolower($type) ?>')">
                <h3 class="text-xl font-bold text-primary mb-4"><?= $type ?> Schools</h3>
                <p class="text-secondary mb-2"><strong>Secondary Dropout:</strong> <?= $data['dropout_rates']['secondary'] ?>%</p>
                <p class="text-gray-600">Click to explore detailed analysis</p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

        <!-- Modals -->
        <?php 
        $categories = [
            'states' => $states,
            'gender' => $genderData,
            'caste' => $casteData,
            'schools' => $schoolTypeData
        ];
        foreach ($categories as $category => $items): 
            foreach ($items as $itemName => $data):
                $modalId = strtolower($category . '-' . str_replace(' ', '-', $itemName));
        ?>
            <div id="<?= $modalId ?>-modal" class="modal">
                <span class="close-modal" onclick="closeModal('<?= $modalId ?>-modal')">×</span>
                <div class="modal-content">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Header -->
                        <div class="bg-secondary text-white p-8">
                            <div class="flex flex-col md:flex-row items-center">
                                <div class="md:w-1/3 mb-6 md:mb-0">
                                    <div class="h-48 w-full rounded-lg overflow-hidden shadow-md">
                                        <img src="india.png"class="h-full w-full object-contain">
                                    </div>
                                </div>
                                <div class="md:w-2/3 md:pl-8">
                                    <h2 class="text-3xl font-bold mb-2"><?= $itemName ?> Dropout Analysis</h2>
                                    <p class="text-light mb-6">Insights into dropout trends for <?= $itemName ?></p>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div class="bg-tertiary p-3 rounded-lg">
                                            <div class="text-xl font-bold">
                                                <?= $data['dropout_rates']['primary'] ?? 'N/A' ?>%
                                            </div>
                                            <div class="text-xs text-light">Primary</div>
                                        </div>
                                        <div class="bg-tertiary p-3 rounded-lg">
                                            <div class="text-xl font-bold">
                                                <?= $data['dropout_rates']['upper_primary'] ?? 'N/A' ?>%
                                            </div>
                                            <div class="text-xs text-light">Upper Primary</div>
                                        </div>
                                        <div class="bg-tertiary p-3 rounded-lg">
                                            <div class="text-xl font-bold">
                                                <?= $data['dropout_rates']['secondary'] ?? 'N/A' ?>%
                                            </div>
                                            <div class="text-xs text-light">Secondary</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-8">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <!-- Causes -->
                                <div>
                                    <h3 class="text-2xl font-bold text-primary mb-6">Primary Causes</h3>
                                    <div class="h-64">
                                        <canvas id="<?= $modalId ?>CausesChart"></canvas>
                                    </div>
                                    <div class="mt-6 space-y-4">
                                        <?php foreach ($data['causes'] as $cause): ?>
                                            <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-accent">
                                                <h4 class="font-semibold text-secondary mb-1">
                                                    <?= $cause['name'] ?> (<?= $cause['percentage'] ?>%)
                                                </h4>
                                                <p class="text-gray-700"></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Strategies -->
                                <div>
                                    <h3 class="text-2xl font-bold text-primary mb-6">Prevention Strategies</h3>
                                    <div class="h-64">
                                        <canvas id="<?= $modalId ?>StrategiesChart"></canvas>
                                    </div>
                                    <div class="mt-6 space-y-4">
                                        <?php foreach ($data['strategies'] as $strategy): ?>
                                            <div class="p-4 bg-gray-50 rounded-lg">
                                                <h4 class="font-semibold text-secondary mb-1 flex items-center">
                                                    <i class="fas fa-check-circle text-accent mr-2"></i>
                                                    <?= $strategy ?>
                                                </h4>
                                                <p class="text-gray-700"></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Key Statistics -->
                            <div class="stats-section">
                                <h3 class="stats-title">Key Statistics</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="chart-container">
                                        <canvas id="<?= $modalId ?>TrendsChart"></canvas>
                                    </div>
                                    <div class="findings-container p-6">
                                        <h4 class="text-xl font-semibold text-secondary mb-4">Comparison with National Trends</h4>
                                        <ul class="space-y-3 text-gray-700">
                                            <li class="flex items-start">
                                                <i class="fas fa-chart-line text-blue-500 mt-1 mr-2"></i>
                                                <span>National Primary Dropout: <?= $nationalTrends['2010-11']['primary'] ?>%</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-chart-line text-blue-500 mt-1 mr-2"></i>
                                                <span>National Upper Primary Dropout: <?= $nationalTrends['2010-11']['upper_primary'] ?>%</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-chart-line text-blue-500 mt-1 mr-2"></i>
                                                <span>National Secondary Dropout: <?= $nationalTrends['2010-11']['secondary'] ?>%</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <footer class="bg-secondary text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4 tracking-tight">DropTrace </h3>
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



    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script>
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        <?php foreach ($categories as $category => $items): 
            foreach ($items as $itemName => $data):
                $chartId = strtolower($category . '-' . str_replace(' ', '-', $itemName));
        ?>
            // Causes Chart
           // Causes Chart with percentage labels
new Chart(
    document.getElementById('<?= $chartId ?>CausesChart').getContext('2d'),
    {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($data['causes'], 'name')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($data['causes'], 'percentage')) ?>,
                backgroundColor: ['#08141B', '#11212D', '#233745', '#4A5C6A', '#9BAAAB'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Primary Causes of Dropouts', 
                    font: { 
                        size: 16, 
                        family: 'Space Grotesk', 
                        weight: '600' 
                    } 
                },
                datalabels: {
                    formatter: (value) => {
                        return value + '%';
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            },
            animation: {
                duration: 0,
                easing: 'easeInOutQuad'
            }
        }
    }
);
            // Strategies Chart
            new Chart(
                    document.getElementById('<?= $chartId ?>StrategiesChart').getContext('2d'),
                    {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($data['strategies']) ?>,
                            datasets: [{
                                label: 'Impact Potential',
                                data: <?= json_encode(array_fill(0, count($data['strategies']), 20)) ?>,
                                backgroundColor: 'rgba(74, 92, 106, 0.7)',
                                borderColor: '#4A5C6A',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: { display: true, text: 'Strategy Impact Potential', font: { size: 16, family: 'Space Grotesk', weight: '600' } }
                            },
                            scales: {
                                y: { beginAtZero: true, title: { display: true, text: 'Impact Score' } }
                            }
                        }
                    }
                );

            // Trends Chart
            new Chart(
                document.getElementById('<?= $chartId ?>TrendsChart').getContext('2d'),
                {
                    type: 'line',
                    data: {
                        labels: ['Primary', 'Upper Primary', 'Secondary'],
                        datasets: [
                            {
                                label: '<?= $itemName ?> Dropout Rate %',
                                data: [
                                    <?= $data['dropout_rates']['primary'] ?? 0 ?>,
                                    <?= $data['dropout_rates']['upper_primary'] ?? 0 ?>,
                                    <?= $data['dropout_rates']['secondary'] ?? 0 ?>
                                ],
                                borderColor: '#4A5C6A',
                                backgroundColor: 'rgba(74, 92, 106, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'National Average %',
                                data: [
                                    <?= $nationalTrends['2010-11']['primary'] ?? 0 ?>,
                                    <?= $nationalTrends['2010-11']['upper_primary'] ?? 0 ?>,
                                    <?= $nationalTrends['2010-11']['secondary'] ?? 0 ?>
                                ],
                                borderColor: '#9BAAAB',
                                backgroundColor: 'rgba(155, 170, 171, 0.1)',
                                borderDash: [5, 5],
                                tension: 0.3
                            }
                        ]
                    },
                    // In the Trends Chart configuration (around line 725)
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        title: { 
            display: true, 
            text: 'Dropout Rates vs National Average', 
            font: { 
                size: 16, 
                family: 'Space Grotesk', 
                weight: '600' 
            } 
        }
    },
    scales: {
        y: { 
            beginAtZero: true, 
            title: { 
                display: true, 
                text: 'Dropout Rate %' 
            } 
        }
    },
    // Add animation configuration here
    animation: {
        duration: 0, // Disable animations
        // OR for smooth animations:
        // duration: 500,
        // easing: 'easeInOutQuad'
    }
}
                }
            );
        <?php endforeach; ?>
        <?php endforeach; ?>
    });
</script>
    <script>
        // Tab switching
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                document.querySelectorAll('.filter-section').forEach(section => {
                    section.classList.add('hidden');
                });
                document.getElementById(tab.dataset.tab + '-section').classList.remove('hidden');
            });
        });

        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($categories as $category => $items): 
                foreach ($items as $itemName => $data):
                    $chartId = strtolower($category . '-' . str_replace(' ', '-', $itemName));
            ?>
                // Causes Chart
                new Chart(
                    document.getElementById('<?= $chartId ?>CausesChart').getContext('2d'),
                    {
                        type: 'doughnut',
                        data: {
                            labels: <?= json_encode(array_column($data['causes'], 'name')) ?>,
                            datasets: [{
                                data: <?= json_encode(array_column($data['causes'], 'percentage')) ?>,
                                backgroundColor: ['#08141B', '#11212D', '#233745', '#4A5C6A', '#9BAAAB'],
                                borderWidth: 0
                            }]
                        },
                        // In the Causes Chart configuration (around line 650)
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        title: { 
            display: true, 
            text: 'Primary Causes of Dropouts', 
            font: { 
                size: 16, 
                family: 'Space Grotesk', 
                weight: '600' 
            } 
        },
        datalabels: {
            formatter: (value) => {
                return value + '%';
            },
            color: '#fff',
            font: {
                weight: 'bold',
                size: 14
            }
        }
    },
    // Add animation configuration here
    animation: {
        duration: 0, // Disable animations completely
        // OR for smooth animations without bounce:
        // duration: 500,
        // easing: 'easeInOutQuad'
    }
}
                    }
                );

                // Strategies Chart
                new Chart(
                    document.getElementById('<?= $chartId ?>StrategiesChart').getContext('2d'),
                    {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($data['strategies']) ?>,
                            datasets: [{
                                label: 'Impact Potential',
                                data: <?= json_encode(array_fill(0, count($data['strategies']), 20)) ?>,
                                backgroundColor: 'rgba(74, 92, 106, 0.7)',
                                borderColor: '#4A5C6A',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: { display: true, text: 'Strategy Impact Potential', font: { size: 16, family: 'Space Grotesk', weight: '600' } }
                            },
                            scales: {
                                y: { beginAtZero: true, title: { display: true, text: 'Impact Score' } }
                            }
                        }
                    }
                );

                // Trends Chart
                new Chart(
                    document.getElementById('<?= $chartId ?>TrendsChart').getContext('2d'),
                    {
                        type: 'line',
                        data: {
                            labels: ['Primary', 'Upper Primary', 'Secondary'],
                            datasets: [
                                {
                                    label: '<?= $itemName ?> Dropout Rate %',
                                    data: [
                                        <?= $data['dropout_rates']['primary'] ?? 0 ?>,
                                        <?= $data['dropout_rates']['upper_primary'] ?? 0 ?>,
                                        <?= $data['dropout_rates']['secondary'] ?? 0 ?>
                                    ],
                                    borderColor: '#4A5C6A',
                                    backgroundColor: 'rgba(74, 92, 106, 0.1)',
                                    tension: 0.3,
                                    fill: true
                                },
                                {
                                    label: 'National Average %',
                                    data: [
                                        <?= $nationalTrends['2010-11']['primary'] ?? 0 ?>,
                                        <?= $nationalTrends['2010-11']['upper_primary'] ?? 0 ?>,
                                        <?= $nationalTrends['2010-11']['secondary'] ?? 0 ?>
                                    ],
                                    borderColor: '#9BAAAB',
                                    backgroundColor: 'rgba(155, 170, 171, 0.1)',
                                    borderDash: [5, 5],
                                    tension: 0.3
                                }
                            ]
                        },
                        // In the Trends Chart configuration (around line 725)
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        title: { 
            display: true, 
            text: 'Dropout Rates vs National Average', 
            font: { 
                size: 16, 
                family: 'Space Grotesk', 
                weight: '600' 
            } 
        }
    },
    scales: {
        y: { 
            beginAtZero: true, 
            title: { 
                display: true, 
                text: 'Dropout Rate %' 
            } 
        }
    },
    // Add animation configuration here
    animation: {
        duration: 0, // Disable animations
        // OR for smooth animations:
        // duration: 500,
        // easing: 'easeInOutQuad'
    }
}
                    }
                );
            <?php endforeach; ?>
            <?php endforeach; ?>
        });

        // Search functionality
    
// Search functionality
document.getElementById('stateSearch').addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    const stateCards = document.querySelectorAll('.state-card');
    let hasVisibleCards = false;

    stateCards.forEach(card => {
        const stateName = card.getAttribute('data-state-name');
        if (stateName.includes(query) && query !== '') {
            card.style.display = 'block';
            hasVisibleCards = true;
        } else {
            card.style.display = 'none';
        }
    });

    // Show/hide no results message
    const noResultsMsg = document.getElementById('noResultsMessage');
    if (!hasVisibleCards && query.length > 0) {
        noResultsMsg.style.display = 'block';
        document.querySelector('#noResultsMessage span').textContent = query;
    } else {
        noResultsMsg.style.display = 'none';
    }

    // Show all cards when search is empty
    if (query === '') {
        stateCards.forEach(card => card.style.display = 'none');
        noResultsMsg.style.display = 'none';
    }
});

        // Modal functions
        function openModal(category, id) {
            document.getElementById(category + '-' + id + '-modal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            document.querySelectorAll('.modal').forEach(modal => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }
    </script>
</body>
</html>
