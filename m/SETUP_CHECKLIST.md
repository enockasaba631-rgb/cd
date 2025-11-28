# ✅ SETUP CHECKLIST

Complete these steps in order:

## Pre-Setup (5 minutes)

- [ ] MySQL is installed and running
- [ ] VS Code is open with the project
- [ ] Live Server extension installed (shows port 5501)

## Setup Steps

### 1. Create Folders (2 minutes)

- [ ] Open PowerShell
- [ ] Navigate to: `c:\Users\Enockieofficial\Desktop\m`
- [ ] Run command: `mkdir uploads, uploads\songs, uploads\images`
- [ ] Verify folders exist in File Explorer

Command to paste in PowerShell:
```powershell
cd "c:\Users\Enockieofficial\Desktop\m" ; mkdir uploads, uploads\songs, uploads\images
```

### 2. Setup Database (5 minutes)

Choose ONE method:

**Method A: phpMyAdmin (Easiest)**
- [ ] Open phpMyAdmin (usually `localhost/phpmyadmin`)
- [ ] Create new database: `my_music_db`
- [ ] Go to Import tab
- [ ] Select `database.sql` file
- [ ] Click Import

**Method B: MySQL Command Line**
- [ ] Open MySQL CLI or terminal
- [ ] Run commands from `database.sql` file

### 3. Configure Database Connection (2 minutes)

- [ ] Open `db_config.php` in VS Code
- [ ] Check these are correct:
  - `DB_SERVER` = `localhost` ✓
  - `DB_USERNAME` = `root` (or your MySQL user)
  - `DB_PASSWORD` = `` (or your password)
  - `DB_NAME` = `my_music_db` ✓

### 4. Verify Setup (3 minutes)

- [ ] Live Server is running (port 5501)
- [ ] Open browser: `http://127.0.0.1:5501/check_system.php`
- [ ] All items show green checkmarks ✓
- [ ] If any red X marks, scroll down for fixes

### 5. Test Upload (5 minutes)

- [ ] Go to: `http://127.0.0.1:5501/index.html`
- [ ] Scroll to "Admin Panel" section
- [ ] Click "Upload Song" button
- [ ] Select an MP3 file (or any: MP3, WAV, OGG, FLAC, M4A)
- [ ] Enter song title
- [ ] Click "Upload Song"
- [ ] See success message ✓

### 6. Verify It Worked (2 minutes)

- [ ] Refresh page: `http://127.0.0.1:5501/index.html`
- [ ] Song appears in music grid ✓
- [ ] Click play button ▶️
- [ ] Song plays in browser ✓
- [ ] Click download button
- [ ] Song downloads ✓

## Done! 🎉

Your gospel music website is fully functional!

### What You Can Do Now

✅ Upload songs via admin panel  
✅ Play songs immediately  
✅ Download songs  
✅ Delete songs from admin  
✅ See latest and popular songs  
✅ Everything responsive on mobile  

---

## Troubleshooting

### Issue: "Folders don't exist" error from check_system.php

**Fix:** Run PowerShell command from Step 1

### Issue: "Database connection failed"

**Fix:**
- Make sure MySQL is running
- Check credentials in `db_config.php` match your MySQL user
- Make sure you imported `database.sql`

### Issue: Upload fails with error message

**Fix:**
- Read the error message - it tells you what's wrong
- Run `check_system.php` to see what's missing
- Fix the issue and try again

### Issue: Upload succeeds but song doesn't appear

**Fix:**
- Check `check_system.php` → look at "Songs in database"
- If it shows 0, the upload actually failed
- Look for error message in browser console (F12)

---

## Quick Reference

| Step | Command / URL |
|------|---------------|
| Create folders | `mkdir uploads, uploads\songs, uploads\images` |
| Check setup | `http://127.0.0.1:5501/check_system.php` |
| Website | `http://127.0.0.1:5501/index.html` |
| Upload test | `http://127.0.0.1:5501/upload_test.html` |

---

## Estimated Time

Total setup time: **15-20 minutes**

- Folders: 2 min
- Database: 5 min
- Configuration: 2 min
- Verification: 3 min
- Testing: 5 min

Then you're done and can start uploading songs! 🎵
