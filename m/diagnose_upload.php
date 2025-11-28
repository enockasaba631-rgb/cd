<?php
header('Content-Type: application/json');

$diagnostics = array();

// 1. Check PHP Upload Settings
$diagnostics['php_settings'] = array(
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'file_uploads_enabled' => ini_get('file_uploads') ? 'Yes' : 'No',
    'max_execution_time' => ini_get('max_execution_time')
);

// 2. Check uploads directory
$uploads_dir = __DIR__ . '/uploads/songs/';
$diagnostics['uploads_directory'] = array(
    'path' => $uploads_dir,
    'exists' => is_dir($uploads_dir),
    'is_writable' => is_writable(__DIR__ . '/uploads')
);

// Create uploads directory if it doesn't exist
if (!is_dir(__DIR__ . '/uploads')) {
    @mkdir(__DIR__ . '/uploads', 0777, true);
}
if (!is_dir($uploads_dir)) {
    @mkdir($uploads_dir, 0777, true);
}

// 2b. Check again after creation
$diagnostics['uploads_directory']['exists_after_creation'] = is_dir($uploads_dir);
$diagnostics['uploads_directory']['is_writable_after_creation'] = is_writable($uploads_dir);

// 3. Check database connection
require_once 'db_config.php';

if ($conn->connect_error) {
    $diagnostics['database'] = array(
        'status' => 'ERROR',
        'message' => $conn->connect_error
    );
} else {
    $diagnostics['database'] = array(
        'status' => 'Connected',
        'database' => DB_NAME,
        'server' => DB_SERVER
    );
    
    // Check if songs table exists
    $result = $conn->query("SHOW TABLES LIKE 'songs'");
    $diagnostics['database']['songs_table_exists'] = ($result && $result->num_rows > 0) ? 'Yes' : 'No';
    
    // Get table structure
    if ($result && $result->num_rows > 0) {
        $columns_result = $conn->query("DESCRIBE songs");
        $columns = array();
        while ($col = $columns_result->fetch_assoc()) {
            $columns[] = $col['Field'] . ' (' . $col['Type'] . ')';
        }
        $diagnostics['database']['songs_table_columns'] = $columns;
    }
}

// 4. Check file permissions
$diagnostics['permissions'] = array(
    'root_dir' => substr(sprintf('%o', fileperms(__DIR__)), -4),
    'uploads_dir' => substr(sprintf('%o', fileperms(__DIR__ . '/uploads')), -4),
    'songs_dir' => is_dir($uploads_dir) ? substr(sprintf('%o', fileperms($uploads_dir)), -4) : 'N/A'
);

// 5. Test file creation
$test_file = $uploads_dir . 'test_' . uniqid() . '.txt';
$diagnostics['file_creation_test'] = array(
    'attempted_file' => $test_file
);

if (@file_put_contents($test_file, 'test')) {
    $diagnostics['file_creation_test']['result'] = 'Success';
    @unlink($test_file);
} else {
    $diagnostics['file_creation_test']['result'] = 'Failed - Cannot write to uploads/songs/';
}

// 6. List existing files in uploads
$diagnostics['existing_files'] = array();
if (is_dir($uploads_dir)) {
    $files = @scandir($uploads_dir);
    if ($files) {
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $diagnostics['existing_files'][] = $file;
            }
        }
    }
}

// 7. Check upload.php file exists
$diagnostics['upload_script'] = array(
    'file_exists' => file_exists(__DIR__ . '/upload.php'),
    'is_readable' => is_readable(__DIR__ . '/upload.php')
);

// 8. Check api_songs.php
$diagnostics['api_script'] = array(
    'file_exists' => file_exists(__DIR__ . '/api_songs.php'),
    'is_readable' => is_readable(__DIR__ . '/api_songs.php')
);

// 9. Test database write
if ($conn->connect_error === null) {
    $test_query = "INSERT INTO songs (title, description, file_path, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($test_query);
    
    if ($stmt) {
        $test_title = 'Test Upload - ' . date('Y-m-d H:i:s');
        $test_path = 'uploads/songs/test.mp3';
        $test_desc = 'This is a test';
        
        $stmt->bind_param('sss', $test_title, $test_desc, $test_path);
        
        if ($stmt->execute()) {
            $song_id = $conn->insert_id;
            $diagnostics['database_write_test'] = array(
                'result' => 'Success',
                'inserted_song_id' => $song_id,
                'message' => 'Database can accept new songs'
            );
            
            // Clean up test entry
            $conn->query("DELETE FROM songs WHERE id = $song_id");
        } else {
            $diagnostics['database_write_test'] = array(
                'result' => 'Failed',
                'error' => $stmt->error
            );
        }
        $stmt->close();
    } else {
        $diagnostics['database_write_test'] = array(
            'result' => 'Failed',
            'error' => 'Could not prepare statement: ' . $conn->error
        );
    }
}

$conn->close();

// Output results
echo json_encode($diagnostics, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
