<?php
// SYSTEM VERIFICATION SCRIPT
header('Content-Type: text/html; charset=utf-8');

$checks = [];

// Check 1: Upload folders
$checks['uploads_folder'] = is_dir(__DIR__ . '/uploads');
$checks['songs_folder'] = is_dir(__DIR__ . '/uploads/songs');
$checks['images_folder'] = is_dir(__DIR__ . '/uploads/images');

// Check 2: Database connection
require_once 'db_config.php';
$checks['database_connected'] = ($conn && !$conn->connect_error);

// Check 3: Songs table
if ($checks['database_connected']) {
    $result = $conn->query("SHOW TABLES LIKE 'songs'");
    $checks['songs_table'] = ($result && $result->num_rows > 0);
    
    // Check 4: Song count
    $result = $conn->query("SELECT COUNT(*) as count FROM songs");
    $row = $result->fetch_assoc();
    $checks['song_count'] = $row['count'];
}

// Check 5: PHP upload limits
$checks['upload_max_filesize'] = ini_get('upload_max_filesize');
$checks['post_max_size'] = ini_get('post_max_size');

$allGood = true;
foreach ($checks as $key => $value) {
    if ($key !== 'song_count' && $key !== 'upload_max_filesize' && $key !== 'post_max_size') {
        if (!$value) $allGood = false;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>System Check</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .ok { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <h1><?php echo $allGood ? '✅ System Ready!' : '❌ Issues Found'; ?></h1>
    
    <div class="status <?php echo $checks['uploads_folder'] ? 'ok' : 'error'; ?>">
        📁 uploads/ folder: <?php echo $checks['uploads_folder'] ? 'EXISTS ✓' : 'MISSING ✗'; ?>
    </div>
    
    <div class="status <?php echo $checks['songs_folder'] ? 'ok' : 'error'; ?>">
        📁 uploads/songs/ folder: <?php echo $checks['songs_folder'] ? 'EXISTS ✓' : 'MISSING ✗'; ?>
    </div>
    
    <div class="status <?php echo $checks['images_folder'] ? 'ok' : 'error'; ?>">
        📁 uploads/images/ folder: <?php echo $checks['images_folder'] ? 'EXISTS ✓' : 'MISSING ✗'; ?>
    </div>
    
    <div class="status <?php echo $checks['database_connected'] ? 'ok' : 'error'; ?>">
        🗄️ Database connection: <?php echo $checks['database_connected'] ? 'CONNECTED ✓' : 'FAILED ✗'; ?>
    </div>
    
    <?php if ($checks['database_connected']): ?>
    <div class="status <?php echo $checks['songs_table'] ? 'ok' : 'error'; ?>">
        🎵 Songs table: <?php echo $checks['songs_table'] ? 'EXISTS ✓' : 'MISSING ✗'; ?>
    </div>
    
    <div class="status warning">
        📊 Songs in database: <?php echo isset($checks['song_count']) ? $checks['song_count'] : '0'; ?>
    </div>
    <?php endif; ?>
    
    <div class="status warning">
        ⚙️ Upload max filesize: <?php echo $checks['upload_max_filesize']; ?>
    </div>
    
    <div class="status warning">
        ⚙️ Post max size: <?php echo $checks['post_max_size']; ?>
    </div>
    
    <hr>
    
    <?php if (!$allGood): ?>
    <h2>⚠️ FIXES NEEDED:</h2>
    
    <?php if (!$checks['uploads_folder'] || !$checks['songs_folder'] || !$checks['images_folder']): ?>
    <p><strong>Run this command in PowerShell:</strong></p>
    <pre>cd "c:\Users\Enockieofficial\Desktop\m" ; mkdir uploads, uploads\songs, uploads\images</pre>
    <?php endif; ?>
    
    <?php if (!$checks['database_connected']): ?>
    <p><strong>Database Issues:</strong></p>
    <ul>
        <li>Make sure MySQL is running</li>
        <li>Check db_config.php for correct credentials</li>
        <li>Import database.sql into phpMyAdmin</li>
    </ul>
    <?php endif; ?>
    
    <?php endif; ?>
    
    <p><a href="http://127.0.0.1:5501/index.html">← Back to Website</a></p>
</body>
</html>
