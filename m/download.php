<?php
require_once 'db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['song_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing song ID']);
    exit;
}

$songId = intval($_POST['song_id']);

// Get song from database
$stmt = $conn->prepare("SELECT id, title, file_path FROM songs WHERE id = ?");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit;
}

$stmt->bind_param("i", $songId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Song not found']);
    $stmt->close();
    $conn->close();
    exit;
}

$song = $result->fetch_assoc();
$filePath = __DIR__ . '/' . $song['file_path'];

// Check if file exists
if (!file_exists($filePath)) {
    echo json_encode(['status' => 'error', 'message' => 'Song file not found on server']);
    $stmt->close();
    $conn->close();
    exit;
}

// Get file size
$fileSize = filesize($filePath);

// Return download info
echo json_encode([
    'status' => 'success',
    'download_url' => 'direct_download.php?id=' . $songId,
    'file_name' => sanitizeFileName($song['title']) . '.' . pathinfo($filePath, PATHINFO_EXTENSION),
    'file_size' => $fileSize
]);

$stmt->close();
$conn->close();

function sanitizeFileName($filename) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}
?>
