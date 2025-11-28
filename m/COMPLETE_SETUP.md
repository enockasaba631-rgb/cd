# COMPLETE SETUP GUIDE - STEP BY STEP

## Step 1: Create Upload Folders ⭐ CRITICAL

Open **PowerShell** (Windows):

1. Press `Windows Key + R` 
2. Type `powershell` and press Enter
3. Copy and paste this command:

```powershell
cd "c:\Users\Enockieofficial\Desktop\m" ; mkdir uploads, uploads\songs, uploads\images
```

4. Press Enter
5. You should see these folders appear in File Explorer

## Step 2: Setup Database

1. Open **phpMyAdmin** or MySQL command line
2. Create a new database named `my_music_db`
3. Import `database.sql` file into it

**Or use MySQL command line:**

```bash
mysql -u root -p
```

Then paste:

```sql
CREATE DATABASE my_music_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE my_music_db;
-- Then import database.sql content
```

## Step 3: Check Database Connection

In VS Code, right-click on `db_config.php` and edit:

```php
<?php
define('DB_SERVER', 'localhost');  // Keep as is
define('DB_USERNAME', 'root');     // Change if your MySQL user is different
define('DB_PASSWORD', '');         // Add password if you have one
define('DB_NAME', 'my_music_db');  // Keep as is
```

## Step 4: Verify Everything Works

1. Open Live Server in VS Code (port 5501)
2. Visit: `http://127.0.0.1:5501/check_system.php`
3. Look for green checkmarks ✓

**If you see red X marks**, scroll down for fixes

## Step 5: Test Upload

1. Visit: `http://127.0.0.1:5501/index.html`
2. Scroll to **Admin Panel**
3. Click **Upload Song**
4. Select an MP3 file
5. Enter song title
6. Click **Upload**

You should see: **"Song uploaded successfully!"**

## Step 6: Verify Upload Worked

1. Refresh the page
2. You should see your song in the music grid
3. Click the play button to test

---

## Troubleshooting

### "Failed to upload song"
- Go to: `http://127.0.0.1:5501/check_system.php`
- See what's red
- Fix accordingly

### Upload folders don't exist
- Run PowerShell command from Step 1

### Database connection failed
- Make sure MySQL is running
- Check credentials in `db_config.php`
- Verify `my_music_db` exists in phpMyAdmin

### Songs don't appear after upload
- Check `http://127.0.0.1:5501/check_system.php` - "Songs in database" count
- If 0, upload failed - check error message

### Can't play uploaded song
- Make sure `uploads/songs/` folder has the file
- Check browser console for errors (F12)

---

## File Structure After Setup

```
c:\Users\Enockieofficial\Desktop\m\
├── index.html
├── upload.php
├── api_songs.php
├── download.php
├── check_system.php
├── db_config.php
├── database.sql
├── uploads/           ← CREATE THIS
│   ├── songs/         ← CREATE THIS
│   └── images/        ← CREATE THIS
└── scripts/
    └── main.js
```

---

## Key Files Overview

| File | Purpose |
|------|---------|
| `index.html` | Website + admin panel |
| `upload.php` | Handles song uploads |
| `api_songs.php` | Gets songs from database |
| `download.php` | Handles song downloads |
| `check_system.php` | Verifies setup |
| `db_config.php` | Database connection |
| `database.sql` | Database schema |
| `scripts/main.js` | All website functionality |

---

## What Happens When You Upload

1. User selects MP3 and enters title in admin panel
2. JavaScript sends file to `upload.php`
3. `upload.php` validates file
4. Saves file to `uploads/songs/filename.mp3`
5. Saves info in MySQL `songs` table
6. Returns success message
7. `main.js` reloads all songs
8. New song appears in music grid

---

## Quick Reference Commands

**Create folders:**
```powershell
cd "c:\Users\Enockieofficial\Desktop\m" ; mkdir uploads, uploads\songs, uploads\images
```

**Delete and recreate folders:**
```powershell
cd "c:\Users\Enockieofficial\Desktop\m" ; rmdir uploads -Force -Recurse ; mkdir uploads, uploads\songs, uploads\images
```

**Check what files uploaded:**
```powershell
dir "c:\Users\Enockieofficial\Desktop\m\uploads\songs\"
```

---

**When you're done with ALL steps, go to: `http://127.0.0.1:5501/index.html` and test!**
