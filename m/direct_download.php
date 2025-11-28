<?php
require_once 'db_config.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo 'Invalid request';
    exit;
}

$songId = intval($_GET['id']);

// Get song from database
$stmt = $conn->prepare("SELECT id, title, file_path FROM songs WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo 'Database error';
    exit;
}

$stmt->bind_param("i", $songId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo 'Song not found';
    $stmt->close();
    $conn->close();
    exit;
}

$song = $result->fetch_assoc();
$filePath = __DIR__ . '/' . $song['file_path'];

// Check if file exists
if (!file_exists($filePath)) {
    http_response_code(404);
    echo 'File not found';
    $stmt->close();
    $conn->close();
    exit;
}

// Update download count
$updateStmt = $conn->prepare("UPDATE songs SET downloads = downloads + 1 WHERE id = ?");
if ($updateStmt) {
    $updateStmt->bind_param("i", $songId);
    $updateStmt->execute();
    $updateStmt->close();
}

// Get file info
$fileName = pathinfo($filePath, PATHINFO_FILENAME);
$fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
$fileSize = filesize($filePath);
$mimeType = getMimeType($fileExt);

// Set headers for download
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . $song['title'] . '.' . $fileExt . '"');
header('Content-Length: ' . $fileSize);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Read and output file
readfile($filePath);

$stmt->close();
$conn->close();
exit;

function getMimeType($ext) {
    $mimeTypes = [
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'flac' => 'audio/flac',
        'm4a' => 'audio/mp4',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp'
    ];
    
    return $mimeTypes[strtolower($ext)] ?? 'application/octet-stream';
}
?>
