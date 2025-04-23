<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$debug_info = '';

// Create necessary tables if they don't exist
try {
    // Create pdf_uploads table
    $table_check = $conn->query("SHOW TABLES LIKE 'pdf_uploads'");
    if ($table_check->rowCount() == 0) {
        $create_table = $conn->prepare("
            CREATE TABLE pdf_uploads (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                file_name VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                upload_date DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        $create_table->execute();
    }

    // Create pdf_questions table
    $table_check = $conn->query("SHOW TABLES LIKE 'pdf_questions'");
    if ($table_check->rowCount() == 0) {
        $create_table = $conn->prepare("
            CREATE TABLE pdf_questions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                pdf_id INT NOT NULL,
                user_id INT NOT NULL,
                question TEXT NOT NULL,
                answer TEXT,
                asked_date DATETIME NOT NULL,
                answered_date DATETIME,
                FOREIGN KEY (pdf_id) REFERENCES pdf_uploads(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        $create_table->execute();
    }
} catch(PDOException $e) {
    $error = 'Error creating tables: ' . $e->getMessage();
    $debug_info = 'SQL Error: ' . $e->getMessage();
}

// Handle PDF upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $debug_info .= "File upload attempt detected.<br>";
    
    // Check if file was actually uploaded
    if ($_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload error: ' . $_FILES['pdf_file']['error'];
        $debug_info .= "Upload error code: " . $_FILES['pdf_file']['error'] . "<br>";
    } else {
        $debug_info .= "File uploaded successfully to temp directory.<br>";
        
        $allowed_types = ['application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $debug_info .= "File type: " . $_FILES['pdf_file']['type'] . "<br>";
        $debug_info .= "File size: " . $_FILES['pdf_file']['size'] . " bytes<br>";
        
        if (!in_array($_FILES['pdf_file']['type'], $allowed_types)) {
            $error = 'Only PDF files are allowed.';
            $debug_info .= "Invalid file type.<br>";
        } elseif ($_FILES['pdf_file']['size'] > $max_size) {
            $error = 'File size must be less than 5MB.';
            $debug_info .= "File too large.<br>";
        } else {
            $upload_dir = 'uploads/pdfs/';
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    $error = 'Failed to create upload directory.';
                    $debug_info .= "Failed to create directory: " . $upload_dir . "<br>";
                } else {
                    $debug_info .= "Created upload directory: " . $upload_dir . "<br>";
                }
            }
            
            if (empty($error)) {
                $file_name = uniqid() . '_' . basename($_FILES['pdf_file']['name']);
                $target_path = $upload_dir . $file_name;
                
                $debug_info .= "Target path: " . $target_path . "<br>";
                
                if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_path)) {
                    $debug_info .= "File moved to target location successfully.<br>";
                    
                    try {
                        $stmt = $conn->prepare("INSERT INTO pdf_uploads (user_id, file_name, original_name, upload_date) VALUES (?, ?, ?, NOW())");
                        $stmt->execute([$_SESSION['user_id'], $file_name, $_FILES['pdf_file']['name']]);
                        $success = 'PDF uploaded successfully.';
                        $debug_info .= "File information stored in database.<br>";
                    } catch(PDOException $e) {
                        $error = 'Error storing PDF information in database: ' . $e->getMessage();
                        $debug_info .= "Database error: " . $e->getMessage() . "<br>";
                        if (file_exists($target_path)) {
                            unlink($target_path);
                            $debug_info .= "Removed uploaded file due to database error.<br>";
                        }
                    }
                } else {
                    $error = 'Error moving uploaded file.';
                    $debug_info .= "Failed to move file to target location.<br>";
                    $debug_info .= "Upload error: " . error_get_last()['message'] . "<br>";
                }
            }
        }
    }
}

// Handle question submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question']) && isset($_POST['pdf_id'])) {
    $question = trim($_POST['question']);
    $pdf_id = $_POST['pdf_id'];
    
    if (empty($question)) {
        $error = 'Please enter a question.';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO pdf_questions (pdf_id, user_id, question, asked_date) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$pdf_id, $_SESSION['user_id'], $question]);
            $success = 'Your question has been submitted. We will analyze the PDF and provide an answer soon.';
        } catch(PDOException $e) {
            $error = 'Error submitting question: ' . $e->getMessage();
        }
    }
}

// Get user's uploaded PDFs and their questions
try {
    $stmt = $conn->prepare("
        SELECT p.*, 
               (SELECT COUNT(*) FROM pdf_questions WHERE pdf_id = p.id) as question_count,
               (SELECT COUNT(*) FROM pdf_questions WHERE pdf_id = p.id AND answer IS NOT NULL) as answered_count
        FROM pdf_uploads p 
        WHERE p.user_id = ? 
        ORDER BY p.upload_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = 'Error fetching PDFs: ' . $e->getMessage();
    $pdfs = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Analysis - Dropout Analysis System</title>
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
    <style>
        .drag-active {
            border-color: #4A5C6A !important;
            background-color: #233745 !important;
        }
        .question-enter {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
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
                    <a href="pdf_analysis.php" class="text-white font-medium">PDF Analysis</a>
                    <a href="profile.php" class="text-light hover:text-white font-medium">Profile</a>
                    <a href="logout.php" class="text-light hover:text-white font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-24">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-primary mb-4">PDF Analysis</h1>
                <p class="text-xl text-secondary">Upload and analyze PDF documents for student data</p>
            </div>

            <!-- Alerts -->
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                    <?php if ($debug_info): ?>
                        <div class="mt-2 text-sm">
                            <p class="font-semibold">Debug Information:</p>
                            <p class="mt-1"><?php echo $debug_info; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <!-- Upload Form -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-12">
                <h2 class="text-2xl font-bold text-primary mb-6">Upload PDF</h2>
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-6" id="uploadForm">
                    <div class="flex items-center justify-center w-full">
                        <label for="pdf_file" id="dropZone" class="flex flex-col items-center justify-center w-full h-64 border-2 border-accent border-dashed rounded-lg cursor-pointer bg-tertiary hover:bg-secondary transition duration-300">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-file-pdf text-4xl text-accent mb-3"></i>
                                <p class="mb-2 text-sm text-light">Click to upload or drag and drop</p>
                                <p class="text-xs text-light">PDF files only (MAX. 5MB)</p>
                            </div>
                            <input id="pdf_file" name="pdf_file" type="file" class="hidden" accept=".pdf" required />
                        </label>
                    </div>
                    <div id="filePreview" class="hidden">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-2xl text-accent mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-primary" id="fileName"></p>
                                    <p class="text-xs text-secondary" id="fileSize"></p>
                                </div>
                            </div>
                            <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" id="uploadButton" class="w-full bg-accent hover:bg-tertiary text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-upload mr-2"></i>Upload PDF
                    </button>
                </form>
            </div>

            <!-- PDF List -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-primary mb-6">Your Uploaded PDFs</h2>
                <?php if (empty($pdfs)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-file-pdf text-4xl text-accent mb-4"></i>
                        <p class="text-secondary">No PDFs uploaded yet.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-6" id="pdfList">
                        <?php foreach ($pdfs as $pdf): ?>
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition duration-300 pdf-card">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-2xl text-accent mr-3"></i>
                                        <div>
                                            <h3 class="text-lg font-semibold text-primary"><?php echo htmlspecialchars($pdf['original_name']); ?></h3>
                                            <p class="text-sm text-secondary">Uploaded on <?php echo date('M d, Y H:i', strtotime($pdf['upload_date'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="uploads/pdfs/<?php echo htmlspecialchars($pdf['file_name']); ?>" 
                                           class="text-accent hover:text-tertiary"
                                           target="_blank">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <a href="analyze_pdf.php?id=<?php echo $pdf['id']; ?>" 
                                           class="text-accent hover:text-tertiary">
                                            <i class="fas fa-chart-bar mr-1"></i>Analyze
                                        </a>
                                    </div>
                                </div>

                                <!-- Questions Section -->
                                <div class="mt-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-md font-semibold text-primary">Questions & Answers</h4>
                                        <span class="text-sm text-secondary">
                                            <?php echo $pdf['answered_count']; ?> of <?php echo $pdf['question_count']; ?> answered
                                        </span>
                                    </div>

                                    <!-- Question Form -->
                                    <form action="" method="POST" class="mb-4">
                                        <input type="hidden" name="pdf_id" value="<?php echo $pdf['id']; ?>">
                                        <div class="flex space-x-2">
                                            <input type="text" 
                                                   name="question" 
                                                   placeholder="Ask a question about this PDF..." 
                                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                                            <button type="submit" 
                                                    class="bg-accent hover:bg-tertiary text-white px-4 py-2 rounded-lg transition duration-300">
                                                <i class="fas fa-paper-plane mr-1"></i>Ask
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Questions List -->
                                    <?php
                                    try {
                                        $stmt = $conn->prepare("
                                            SELECT * FROM pdf_questions 
                                            WHERE pdf_id = ? 
                                            ORDER BY asked_date DESC
                                        ");
                                        $stmt->execute([$pdf['id']]);
                                        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    } catch(PDOException $e) {
                                        $questions = [];
                                    }
                                    ?>

                                    <?php if (!empty($questions)): ?>
                                        <div class="space-y-4">
                                            <?php foreach ($questions as $question): ?>
                                                <div class="border-l-4 border-accent pl-4">
                                                    <div class="text-sm text-secondary mb-1">
                                                        Asked on <?php echo date('M d, Y H:i', strtotime($question['asked_date'])); ?>
                                                    </div>
                                                    <p class="text-primary font-medium mb-2"><?php echo htmlspecialchars($question['question']); ?></p>
                                                    <?php if ($question['answer']): ?>
                                                        <div class="bg-gray-50 p-3 rounded-lg">
                                                            <div class="text-sm text-secondary mb-1">
                                                                Answered on <?php echo date('M d, Y H:i', strtotime($question['answered_date'])); ?>
                                                            </div>
                                                            <p class="text-primary"><?php echo htmlspecialchars($question['answer']); ?></p>
                                                        </div>
                                                    <?php else: ?>
                                                        <p class="text-sm text-secondary">Answer pending...</p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-sm text-secondary">No questions asked yet.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Drag and drop functionality
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('pdf_file');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const removeFile = document.getElementById('removeFile');
            const uploadButton = document.getElementById('uploadButton');

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            // Highlight drop zone when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            // Handle dropped files
            dropZone.addEventListener('drop', handleDrop, false);

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function highlight(e) {
                dropZone.classList.add('drag-active');
            }

            function unhighlight(e) {
                dropZone.classList.remove('drag-active');
            }

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }

            function handleFiles(files) {
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type === 'application/pdf') {
                        fileInput.files = files;
                        showFilePreview(file);
                    } else {
                        alert('Please upload a PDF file only.');
                    }
                }
            }

            function showFilePreview(file) {
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.classList.remove('hidden');
                uploadButton.disabled = false;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Handle file input change
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFiles(this.files);
                }
            });

            // Handle file removal
            removeFile.addEventListener('click', function() {
                fileInput.value = '';
                filePreview.classList.add('hidden');
                uploadButton.disabled = true;
            });

            // Question submission handling
            const questionForms = document.querySelectorAll('form[action=""]');
            questionForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const questionInput = this.querySelector('input[name="question"]');
                    const question = questionInput.value.trim();

                    if (question) {
                        fetch('', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(html => {
                            // Create a temporary container
                            const temp = document.createElement('div');
                            temp.innerHTML = html;

                            // Find the new question in the response
                            const newQuestion = temp.querySelector('.question-enter');
                            if (newQuestion) {
                                // Add the new question to the questions list
                                const questionsList = this.nextElementSibling.querySelector('.space-y-4') || 
                                                    this.nextElementSibling.querySelector('.text-sm');
                                if (questionsList) {
                                    if (questionsList.classList.contains('text-sm')) {
                                        questionsList.outerHTML = '<div class="space-y-4">' + newQuestion.outerHTML + '</div>';
                                    } else {
                                        questionsList.insertAdjacentHTML('afterbegin', newQuestion.outerHTML);
                                    }
                                }
                            }

                            // Clear the input
                            questionInput.value = '';
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while submitting your question.');
                        });
                    }
                });
            });

            // PDF card animations
            const pdfCards = document.querySelectorAll('.pdf-card');
            pdfCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // File size validation
            const maxSize = 5 * 1024 * 1024; // 5MB
            fileInput.addEventListener('change', function() {
                if (this.files[0].size > maxSize) {
                    alert('File size must be less than 5MB.');
                    this.value = '';
                    filePreview.classList.add('hidden');
                    uploadButton.disabled = true;
                }
            });
        });
    </script>
</body>
</html> 