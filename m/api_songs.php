<?php
require_once 'db_config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        getSongs();
        break;
    case 'get':
        getSongById();
        break;
    case 'delete':
        deleteSong();
        break;
    case 'update':
        updateSong();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function getSongs() {
    global $conn;
    
    $filter = $_GET['filter'] ?? 'all';
    
    $query = "SELECT id, title, description, image_path, file_path, duration, downloads, created_at FROM songs";
    
    if ($filter === 'latest') {
        $query .= " ORDER BY created_at DESC LIMIT 10";
    } elseif ($filter === 'popular') {
        $query .= " ORDER BY downloads DESC LIMIT 10";
    } else {
        $query .= " ORDER BY created_at DESC";
    }
    
    $result = $conn->query($query);
    
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Query failed']);
        return;
    }
    
    $songs = [];
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'songs' => $songs,
        'count' => count($songs)
    ]);
}

function getSongById() {
    global $conn;
    
    if (!isset($_GET['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing song ID']);
        return;
    }
    
    $songId = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT id, title, description, image_path, file_path, duration, downloads, created_at FROM songs WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        return;
    }
    
    $stmt->bind_param("i", $songId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Song not found']);
        $stmt->close();
        return;
    }
    
    $song = $result->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'song' => $song
    ]);
    
    $stmt->close();
}

function deleteSong() {
    global $conn;
    
    if (!isset($_POST['song_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing song ID']);
        return;
    }
    
    $songId = intval($_POST['song_id']);
    
    // Get song file path
    $stmt = $conn->prepare("SELECT file_path, image_path FROM songs WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        return;
    }
    
    $stmt->bind_param("i", $songId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Song not found']);
        $stmt->close();
        return;
    }
    
    $song = $result->fetch_assoc();
    $stmt->close();
    
    // Delete files
    $audioPath = __DIR__ . '/' . $song['file_path'];
    $imagePath = __DIR__ . '/' . $song['image_path'];
    
    if (file_exists($audioPath)) {
        unlink($audioPath);
    }
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
    
    // Delete from database
    $deleteStmt = $conn->prepare("DELETE FROM songs WHERE id = ?");
    if (!$deleteStmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        return;
    }
    
    $deleteStmt->bind_param("i", $songId);
    
    if ($deleteStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Song deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete song']);
    }
    
    $deleteStmt->close();
}

function updateSong() {
    global $conn;
    
    if (!isset($_POST['song_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing song ID']);
        return;
    }
    
    $songId = intval($_POST['song_id']);
    $title = htmlspecialchars($_POST['title'] ?? '');
    $description = htmlspecialchars($_POST['description'] ?? '');
    
    $stmt = $conn->prepare("UPDATE songs SET title = ?, description = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        return;
    }
    
    $stmt->bind_param("ssi", $title, $description, $songId);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Song updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update song']);
    }
    
    $stmt->close();
}

$conn->close();
?>
