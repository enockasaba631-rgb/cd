<?php
header('Content-Type: application/json');

require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Validate inputs
if (!isset($_FILES['audio-file']) || $_FILES['audio-file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No audio file provided']);
    exit;
}

if (!isset($_POST['song-title']) || empty(trim($_POST['song-title']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Song title is required']);
    exit;
}

$title = trim($_POST['song-title']);
$description = isset($_POST['song-description']) ? trim($_POST['song-description']) : '';
$file = $_FILES['audio-file'];

// Validate file type
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['mp3', 'wav', 'ogg', 'flac', 'm4a'];

if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)]);
    exit;
}

// Validate file size (max 100MB)
if ($file['size'] > 100 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'File too large. Max 100MB']);
    exit;
}

// Create upload directory
$dir = __DIR__ . '/uploads/songs/';
if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
}

// Check if directory was created
if (!is_dir($dir)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Cannot create uploads directory']);
    exit;
}

// Generate filename and save
$filename = uniqid('song_', true) . '.' . $ext;
$filepath = $dir . $filename;
$dbpath = 'uploads/songs/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save file']);
    exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO songs (title, description, file_path, image_path, duration, downloads, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

if (!$stmt) {
    @unlink($filepath);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$image_path = '';
$duration = 0;
$downloads = 0;

$stmt->bind_param('ssssii', $title, $description, $dbpath, $image_path, $duration, $downloads);

if (!$stmt->execute()) {
    @unlink($filepath);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$song_id = $conn->insert_id;
$stmt->close();
$conn->close();

http_response_code(201);
echo json_encode([
    'status' => 'success',
    'message' => 'Song uploaded successfully!',
    'song_id' => $song_id,
    'title' => $title,
    'file_path' => $dbpath
]);
