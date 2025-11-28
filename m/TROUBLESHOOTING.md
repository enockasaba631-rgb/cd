# ❌ "Failed to upload song" - TROUBLESHOOTING GUIDE

## 🔍 DIAGNOSIS STEPS

### Step 1: Run the Test Script
1. Open your browser
2. Go to: `http://localhost:8000/test_setup.php` (or your server URL + /test_setup.php)
3. This will show you exactly what's wrong

---

## 🛠️ COMMON PROBLEMS & SOLUTIONS

### Problem 1: "Failed to upload audio file"

**Cause 1: Missing/unwritable uploads folder**
```
Solution:
1. Create these folders manually:
   📁 uploads/
   ├── 📁 songs/
   └── 📁 images/

2. Set permissions:
   Windows: Usually automatic with XAMPP
   Mac/Linux: chmod 777 uploads/
             chmod 777 uploads/songs/
             chmod 777 uploads/images/
```

**Cause 2: File too large**
```
Solution:
1. Make sure your audio file is LESS than 100MB
2. Or edit upload.php line 52:
   if ($audioFile['size'] > 100 * 1024 * 1024) {
   Change 100 to a larger number (e.g., 500 for 500MB)
```

**Cause 3: Wrong file format**
```
Solution:
1. Use these formats ONLY:
   ✅ MP3
   ✅ WAV
   ✅ OGG
   ✅ FLAC
   ✅ M4A
   
2. Do NOT use:
   ❌ WMA
   ❌ AAC (unless as .m4a)
   ❌ OPUS
```

---

### Problem 2: "Failed to save song to database"

**Cause 1: Database not imported**
```
Solution:
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "Import" tab
3. Choose database.sql file
4. Click "Go"
5. Wait for success message
```

**Cause 2: Database connection error**
```
Solution:
1. Edit db_config.php
2. Check these are correct:
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_NAME', 'my_music_db');

3. If MySQL password is set, add it:
   define('DB_PASSWORD', 'your_password');

4. Save and try again
```

**Cause 3: Songs table doesn't exist**
```
Solution:
1. Check if database.sql was imported correctly
2. Go to phpMyAdmin
3. Look for "my_music_db" database
4. Click on it
5. Check if "songs" table exists
6. If not, import database.sql again
```

---

### Problem 3: "Missing required fields"

**Solution:**
```
Make sure you filled in:
✅ Song Title (required)
⚠️ Description (optional but recommended)
✅ Audio File (required - MP3, WAV, OGG, FLAC, M4A)
⚠️ Cover Image (optional)

The error means at least one REQUIRED field is empty.
```

---

### Problem 4: MySQL Connection Fails

**Symptom:** Homepage shows "Connection failed"

**Cause: MySQL not running**
```
Solution for XAMPP:
1. Open XAMPP Control Panel
2. Look for "MySQL"
3. Click "Start" button
4. Wait for it to turn green
5. Refresh website

If still not working:
1. Click "Config" button for MySQL
2. Check if default port is 3306
3. Update db_config.php if different:
   define('DB_SERVER', 'localhost:3306');
```

**Cause: Wrong credentials**
```
Solution:
1. In XAMPP, go to phpMyAdmin
2. See what username/password works
3. Update db_config.php with those credentials
4. Usually username is "root" with empty password
```

---

## ✅ STEP-BY-STEP FIX

### Fix 1: Create Upload Folders (if missing)

**On Windows PowerShell:**
```powershell
cd c:\Users\Enockieofficial\Desktop\m
mkdir uploads
mkdir uploads\songs
mkdir uploads\images
```

**On Mac/Linux Terminal:**
```bash
cd ~/Desktop/m  # or your path
mkdir -p uploads/songs
mkdir -p uploads/images
chmod 777 -R uploads/
```

### Fix 2: Verify Database

**In phpMyAdmin:**
1. Go to http://localhost/phpmyadmin
2. Look for "my_music_db" database on left
3. If it's there, click it
4. Look for "songs" table
5. If not there, import database.sql again

**Steps to import database.sql:**
1. In phpMyAdmin, click "Import" tab
2. Click "Choose File"
3. Select database.sql from your project
4. Click "Go"
5. Wait for "Import has been executed successfully"

### Fix 3: Update Database Config

**Edit db_config.php:**
```php
<?php
define('DB_SERVER', 'localhost');    // Change if not localhost
define('DB_USERNAME', 'root');       // Change if different
define('DB_PASSWORD', '');           // Add password if you have one
define('DB_NAME', 'my_music_db');   // Don't change this
?>
```

### Fix 4: Test Everything

1. Run http://localhost:8000/test_setup.php
2. Check if all tests show ✅
3. If any show ❌, fix that issue first
4. Try uploading again

---

## 🧪 MANUAL TEST (Advanced)

### Test from Command Line:

**Test 1: Can PHP connect to MySQL?**
```php
php -r "
require 'db_config.php';
if ($conn->connect_error) {
    echo 'Connection failed: ' . $conn->connect_error;
} else {
    echo 'Connected successfully!';
}
"
```

**Test 2: Does songs table exist?**
```php
php -r "
require 'db_config.php';
\$result = \$conn->query('SHOW TABLES LIKE \"songs\"');
if (\$result->num_rows > 0) {
    echo 'Songs table exists!';
} else {
    echo 'Songs table NOT found!';
}
"
```

**Test 3: Can we insert a test song?**
```php
php -r "
require 'db_config.php';
\$stmt = \$conn->prepare('INSERT INTO songs (title, file_path) VALUES (?, ?)');
\$title = 'Test';
\$path = 'test.mp3';
\$stmt->bind_param('ss', \$title, \$path);
if (\$stmt->execute()) {
    echo 'Insert successful!';
} else {
    echo 'Insert failed: ' . \$conn->error;
}
"
```

---

## 📋 CHECKLIST BEFORE UPLOADING

- [ ] Created uploads/ folder
- [ ] Created uploads/songs/ folder
- [ ] Created uploads/images/ folder
- [ ] Imported database.sql
- [ ] Verified my_music_db database exists
- [ ] Verified songs table exists
- [ ] Updated db_config.php if needed
- [ ] MySQL is running
- [ ] Web server is running
- [ ] Can see homepage
- [ ] test_setup.php shows all ✅
- [ ] Audio file is MP3/WAV/OGG/FLAC/M4A
- [ ] Audio file is less than 100MB
- [ ] Song title is filled in
- [ ] Tried uploading again

---

## 📞 IF STILL NOT WORKING

### Step 1: Open Browser Console
```
Press F12 → Click "Console" tab
Try uploading again
Look for error messages
Screenshot the error
```

### Step 2: Check Server Logs
**XAMPP:**
1. Open XAMPP Control Panel
2. Click "Logs" for Apache
3. Look for errors near upload time

**PHP Built-in Server:**
1. Look at terminal where you ran: `php -S localhost:8000`
2. You'll see errors there

### Step 3: Check PHP Error Log
```
Look for file: php_error.log
Usually in XAMPP/php folder
Open with text editor
Search for recent errors
```

---

## 🎯 QUICK DIAGNOSIS

Run this and tell me what it shows:

**Copy this code to a file called `diagnose.php`:**
```php
<?php
echo "=== DIAGNOSIS REPORT ===\n";

// Test 1
echo "\n1. PHP Version: " . phpversion();

// Test 2
echo "\n2. MySQL Extension: ";
echo extension_loaded('mysqli') ? "✅ Loaded" : "❌ NOT LOADED";

// Test 3
echo "\n3. Max Upload: " . ini_get('upload_max_filesize');

// Test 4
echo "\n4. Database Connection: ";
require 'db_config.php';
echo $conn->connect_error ? "❌ " . $conn->connect_error : "✅ Connected";

// Test 5
echo "\n5. Songs Table: ";
$result = $conn->query("SHOW TABLES LIKE 'songs'");
echo $result->num_rows > 0 ? "✅ Exists" : "❌ Not found";

// Test 6
echo "\n6. Upload Folder: ";
echo is_dir('uploads/songs') ? "✅ Exists" : "❌ Not found";

echo "\n\n=== END REPORT ===\n";
?>
```

Run it: `http://localhost:8000/diagnose.php`

---

## 💡 QUICK FIXES (Try These First)

### Fix 1: Recreate Upload Folders
```
Delete:  uploads/ folder completely
Create:  uploads/ with songs/ and images/ inside
Try uploading again
```

### Fix 2: Reimport Database
```
In phpMyAdmin:
1. Delete my_music_db database
2. Import database.sql again
3. Try uploading
```

### Fix 3: Check MySQL is Running
```
XAMPP: Make sure MySQL shows "Running" (green)
If not: Click "Start" button
Try uploading again
```

### Fix 4: Restart Everything
```
1. Stop Apache
2. Stop MySQL
3. Close browser
4. Start Apache
5. Start MySQL
6. Refresh browser
7. Try uploading
```

---

**Once you run test_setup.php and see what's wrong, you'll know exactly how to fix it!**

Let me know what the test shows! 🎵
