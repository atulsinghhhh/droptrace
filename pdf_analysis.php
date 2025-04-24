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
}

// Handle PDF upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $allowed_types = ['application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($_FILES['pdf_file']['type'], $allowed_types)) {
        $error = 'Only PDF files are allowed.';
    } elseif ($_FILES['pdf_file']['size'] > $max_size) {
        $error = 'File size must be less than 5MB.';
    } else {
        $upload_dir = __DIR__ . '/uploads/pdfs/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = uniqid() . '_' . basename($_FILES['pdf_file']['name']);
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_path)) {
            try {
                $stmt = $conn->prepare("INSERT INTO pdf_uploads (user_id, file_name, original_name, upload_date) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$_SESSION['user_id'], $file_name, $_FILES['pdf_file']['name']]);
                $success = 'PDF uploaded successfully!';
            } catch(PDOException $e) {
                $error = 'Error storing file information in database.';
                unlink($target_path);
            }
        } else {
            $error = 'Failed to move uploaded file.';
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
            $success = 'Your question has been submitted. We will provide an answer soon.';
        } catch(PDOException $e) {
            $error = 'Error submitting question: ' . $e->getMessage();
        }
    }
}

// Handle answer submission (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer']) && isset($_POST['question_id'])) {
    $answer = trim($_POST['answer']);
    $question_id = $_POST['question_id'];
    
    if (empty($answer)) {
        $error = 'Please enter an answer.';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE pdf_questions SET answer = ?, answered_date = NOW() WHERE id = ?");
            $stmt->execute([$answer, $question_id]);
            $success = 'Answer has been submitted successfully!';
        } catch(PDOException $e) {
            $error = 'Error submitting answer: ' . $e->getMessage();
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

// Get all unanswered questions
$unanswered_questions = [];
try {
    $stmt = $conn->prepare("
        SELECT q.*, p.original_name as pdf_name, u.username
        FROM pdf_questions q
        JOIN pdf_uploads p ON q.pdf_id = p.id
        JOIN users u ON q.user_id = u.id
        WHERE q.answer IS NULL
        ORDER BY q.asked_date DESC
    ");
    $stmt->execute();
    $unanswered_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = 'Error fetching unanswered questions: ' . $e->getMessage();
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
    <style>
        .drag-active {
            border-color: #4A5C6A !important;
            background-color: #233745 !important;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navigation Bar -->
    <nav class="gradient-bg text-white shadow-lg fixed top-0 left-0 w-full z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-graduation-cap text-2xl text-blue-400 mr-2"></i>
                    <span class="font-semibold text-xl tracking-tight">DropTrace</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-gray-300 hover:text-white transition duration-300">Home</a>
                    <a href="analysis.php" class="text-gray-300 hover:text-white transition duration-300">Analysis</a>
                    <a href="intervensions.php" class="text-gray-300 hover:text-white transition duration-300">Interventions</a>
                    <a href="aboutus.php" class="text-gray-300 hover:text-white transition duration-300">About Us</a>
                    <a href="pdf_analysis.php" class="text-white font-medium">PDF Analysis</a>
                    <a href="profile.php" class="text-gray-300 hover:text-white transition duration-300">Profile</a>
                    <a href="logout.php" class="text-gray-300 hover:text-white transition duration-300">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-24">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-16">
                <h1 class="text-5xl font-bold text-gray-800 mb-4">PDF Analysis</h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Upload and analyze PDF documents to extract valuable insights about student data</p>
            </div>

            <!-- Alerts -->
            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-8 rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span><?php echo $success; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Unanswered Questions Section -->
            <?php if (!empty($unanswered_questions)): ?>
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Questions Waiting for Answers</h2>
                    <div class="space-y-6">
                        <?php foreach ($unanswered_questions as $question): ?>
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($question['pdf_name']); ?></h3>
                                        <p class="text-sm text-gray-500">Asked by <?php echo htmlspecialchars($question['username']); ?></p>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        <?php echo date('M d, Y H:i', strtotime($question['asked_date'])); ?>
                                    </span>
                                </div>
                                <div class="mb-4">
                                    <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($question['question']); ?></p>
                                </div>
                                <form action="" method="POST" class="space-y-4">
                                    <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                    <div>
                                        <label for="answer_<?php echo $question['id']; ?>" class="block text-sm font-medium text-gray-700 mb-2">Provide Answer</label>
                                        <textarea id="answer_<?php echo $question['id']; ?>" 
                                                  name="answer" 
                                                  rows="3" 
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                  placeholder="Enter your answer here..."></textarea>
                                    </div>
                                    <button type="submit" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                                        Submit Answer
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Upload Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-12 card-hover">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Upload PDF</h2>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Max file size: 5MB
                    </div>
                </div>
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-6" id="uploadForm">
                    <div class="flex items-center justify-center w-full">
                        <label for="pdf_file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-300">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-file-pdf text-5xl text-blue-500 mb-4"></i>
                                <p class="mb-2 text-lg text-gray-600">Click to upload or drag and drop</p>
                                <p class="text-sm text-gray-500">PDF files only</p>
                            </div>
                            <input id="pdf_file" name="pdf_file" type="file" class="hidden" accept=".pdf" />
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl transition duration-300 transform hover:scale-105">
                        <i class="fas fa-upload mr-2"></i>Upload PDF
                    </button>
                </form>
            </div>

            <!-- PDF List -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Your Uploaded PDFs</h2>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-file-pdf mr-1"></i>
                        <?php echo count($pdfs); ?> documents
                    </div>
                </div>

                <?php if (empty($pdfs)): ?>
                    <div class="text-center py-16">
                        <i class="fas fa-file-pdf text-6xl text-blue-400 mb-6"></i>
                        <p class="text-xl text-gray-600 mb-4">No PDFs uploaded yet</p>
                        <p class="text-gray-500">Upload your first PDF document to get started</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($pdfs as $pdf): ?>
                            <div class="bg-gray-50 rounded-xl p-6 card-hover">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex items-start">
                                        <i class="fas fa-file-pdf text-3xl text-blue-500 mr-4 mt-1"></i>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($pdf['original_name']); ?></h3>
                                            <p class="text-sm text-gray-500">Uploaded <?php echo date('M d, Y', strtotime($pdf['upload_date'])); ?></p>
                                        </div>
                                    </div>
                                    <a href="uploads/pdfs/<?php echo htmlspecialchars($pdf['file_name']); ?>" 
                                       class="text-blue-500 hover:text-blue-600 transition duration-300"
                                       target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>

                                <!-- Questions Section -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-md font-semibold text-gray-800">Questions & Answers</h4>
                                        <span class="text-sm text-gray-500">
                                            <?php echo $pdf['answered_count']; ?>/<?php echo $pdf['question_count']; ?> answered
                                        </span>
                                    </div>

                                    <!-- Question Form -->
                                    <form action="" method="POST" class="mb-4">
                                        <input type="hidden" name="pdf_id" value="<?php echo $pdf['id']; ?>">
                                        <div class="flex space-x-2">
                                            <input type="text" 
                                                   name="question" 
                                                   placeholder="Ask a question..." 
                                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <button type="submit" 
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg transition duration-300">
                                                <i class="fas fa-paper-plane"></i>
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
                                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0">
                                                            <i class="fas fa-question-circle text-blue-400 text-lg"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="text-sm text-gray-500 mb-1">
                                                                Asked <?php echo date('M d, Y H:i', strtotime($question['asked_date'])); ?>
                                                            </div>
                                                            <p class="text-gray-800 font-medium mb-2"><?php echo htmlspecialchars($question['question']); ?></p>
                                                            <?php if ($question['answer']): ?>
                                                                <div class="bg-blue-50 rounded-lg p-3 mt-2">
                                                                    <div class="text-sm text-gray-500 mb-1">
                                                                        Answered <?php echo date('M d, Y H:i', strtotime($question['answered_date'])); ?>
                                                                    </div>
                                                                    <p class="text-gray-800"><?php echo htmlspecialchars($question['answer']); ?></p>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="bg-gray-50 rounded-lg p-3 mt-2">
                                                                    <p class="text-sm text-gray-500">Answer pending...</p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <p class="text-gray-500">No questions asked yet</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Handle file input change
        document.getElementById('pdf_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const label = this.parentElement;
                label.querySelector('p:first-of-type').textContent = fileName;
            }
        });

        // Handle drag and drop
        const dropZone = document.querySelector('label[for="pdf_file"]');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('drag-active');
        }

        function unhighlight(e) {
            dropZone.classList.remove('drag-active');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('pdf_file').files = files;
            
            const fileName = files[0]?.name;
            if (fileName) {
                dropZone.querySelector('p:first-of-type').textContent = fileName;
            }
        }
    </script>
</body>
</html>


