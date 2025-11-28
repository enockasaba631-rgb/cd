REMOVED: This file was cleared and backed up to `removed_files/backups/SETUP_GUIDE.md.bak.md` per user request on 2025-11-28.
If you need to restore it, copy the backup file back to this location.
- **MySQL Server 5.7+** or **MariaDB**
- **Web Server** (Apache, Nginx, or use PHP built-in server)

#### Option A: Using XAMPP (Recommended for beginners)
1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL from XAMPP Control Panel
3. Copy your project to `C:\xampp\htdocs\enockie` (Windows) or `/Applications/XAMPP/htdocs/enockie` (Mac)

#### Option B: Using built-in PHP server (Quick test)
```powershell
cd c:\Users\Enockieofficial\Desktop\m
php -S localhost:8000
```
Then visit `http://localhost:8000` in your browser.

---

### 2. **Create the Database**

#### Method 1: Using phpMyAdmin (XAMPP)
1. Open `http://localhost/phpmyadmin`
2. Go to "Import" tab
3. Select `database.sql` from your project folder
4. Click "Go"

#### Method 2: Using MySQL Command Line
```bash
mysql -u root -p < database.sql
```

#### Method 3: Manual Database Creation
1. Open MySQL command line or phpMyAdmin
2. Copy-paste the SQL commands from `database.sql`
3. Execute each command

---

### 3. **Configure Database Connection**

Edit `db_config.php` with your database credentials:

```php
<?php
define('DB_SERVER', 'localhost');      // Your MySQL server (usually localhost)
define('DB_USERNAME', 'root');         // Your MySQL username
define('DB_PASSWORD', '');             // Your MySQL password (usually empty for local)
define('DB_NAME', 'my_music_db');     // Database name
?>
```

---

### 4. **Create Upload Folder**

The website needs a folder for storing uploaded songs. Create this folder in your project:

```
📁 enockie/
  ├── 📁 uploads/
  │   ├── 📁 songs/        (for audio files)
  │   └── 📁 images/       (for cover images)
  ├── index.html
  ├── db_config.php
  └── upload.php
```

**On Windows PowerShell:**
```powershell
mkdir uploads/songs
mkdir uploads/images
```

**On Mac/Linux:**
```bash
mkdir -p uploads/songs
mkdir -p uploads/images
```

---

### 5. **Set Folder Permissions**

The web server needs permission to write to the uploads folder.

**On Windows:** Usually automatic with XAMPP

**On Mac/Linux:**
```bash
chmod 755 uploads/
chmod 755 uploads/songs/
chmod 755 uploads/images/
```

---

### 6. **Access the Website**

#### Using XAMPP:
```
http://localhost/enockie
```

#### Using Built-in PHP Server:
```
http://localhost:8000
```

---

## 🔐 Admin Login

**Access Admin Dashboard:**
1. Click "Admin" button in header
2. Enter credentials:
   - **Username:** `admin`
   - **Password:** `password`

**Admin Features:**
- Dashboard with statistics
- Upload new songs
- Manage existing songs
- View download statistics

---

## 📁 File Structure

```
📁 enockie/
│
├── 📄 index.html              (Main website - displays songs, player, contact)
├── 📄 db_config.php           (Database connection configuration)
├── 📄 database.sql            (Database schema and tables)
├── 📄 upload.php              (Handles song uploads)
├── 📄 download.php            (Handles song downloads)
├── 📄 direct_download.php     (Direct file download)
├── 📄 api_songs.php           (API for managing songs)
├── 📄 README_DATABASE.md      (Database documentation)
│
├── 📁 uploads/
│   ├── 📁 songs/              (Uploaded MP3 files)
│   └── 📁 images/             (Cover images)
│
├── 📁 scripts/
│   └── 📄 main.js             (JavaScript functionality)
│
└── 📁 styles/
    └── 📄 style.css           (CSS styling)
```

---

## 🎵 Uploading Songs

1. **Login to Admin Dashboard**
   - Click "Admin" → Enter credentials

2. **Go to Upload Panel**
   - Click "Upload Song" tab

3. **Fill in the Form**
   - Song Title: (required)
   - Description: (optional, but recommended)
   - Cover Image: (optional, must be JPG/PNG/GIF)
   - Audio File: (required, must be MP3/WAV/OGG/FLAC/M4A)

4. **Submit**
   - Click "Upload Song"
   - Wait for success message
   - Song appears on main page immediately

---

## 📥 How Users Download Songs

1. **Browse Songs**
   - Visitors see all songs on the website

2. **Click Download Button**
   - Click the download icon on any song

3. **Save File**
   - Browser downloads the MP3 file
   - Download count updates automatically

---

## 🎮 Player Controls

- **Play/Pause:** Click the play button
- **Progress Bar:** Click to skip to a position
- **Next/Previous:** Use navigation buttons
- **Close Player:** Click X button

---

## 🔧 Troubleshooting

### Problem: "Connection failed"
**Solution:** 
- Check MySQL is running
- Verify credentials in `db_config.php`
- Check database name matches

### Problem: "Upload folder not found"
**Solution:**
- Create `uploads/songs` and `uploads/images` folders manually
- Set proper permissions (chmod 755)

### Problem: "File upload failed"
**Solution:**
- Check file size (max 100MB for audio, 10MB for images)
- Verify file format (MP3, WAV, OGG for audio; JPG, PNG, GIF for images)
- Check uploads folder has write permissions

### Problem: "Songs not appearing"
**Solution:**
- Refresh the page
- Check browser console for errors (F12)
- Verify database has songs (check phpMyAdmin)

### Problem: "Download not working"
**Solution:**
- Check file exists in uploads/songs folder
- Verify file permissions
- Check file path in database

---

## 🔒 Security Notes

### For Production:

1. **Change Admin Password**
   - Edit `scripts/main.js` (around line 220)
   - Change username and password check

2. **Use HTTPS**
   - Install SSL certificate
   - Update all URLs to use HTTPS

3. **Validate Uploads**
   - The code already validates file types and sizes
   - Add additional validation as needed

4. **Protect Admin Area**
   - Implement proper authentication with sessions
   - Store passwords as hashes (use password_hash in PHP)

5. **Database Security**
   - Use strong MySQL password
   - Restrict database user permissions
   - Regular backups

---

## 📊 Database Schema

### Songs Table
- `id`: Unique song ID
- `title`: Song name
- `description`: Song description
- `file_path`: Path to audio file
- `image_path`: Path to cover image
- `duration`: Song length in seconds
- `downloads`: Download count
- `created_at`: Upload date

### Users Table
- `id`: Unique user ID
- `username`: User login name
- `email`: User email
- `password`: Hashed password
- `full_name`: Full name

### Other Tables
- `playlists`: User-created playlists
- `favorites`: Bookmarked songs
- `comments`: Song reviews and ratings
- `playlist_songs`: Songs in playlists

---

## 📝 API Endpoints

### Get All Songs
```
GET /api_songs.php?action=list
GET /api_songs.php?action=list&filter=latest
GET /api_songs.php?action=list&filter=popular
```

### Get Single Song
```
GET /api_songs.php?action=get&id=1
```

### Download Song
```
GET /direct_download.php?id=1
```

### Upload Song
```
POST /upload.php
(multipart form data with song-title, song-description, audio-file, song-image)
```

---

## 🎓 Learning Resources

- **PHP:** [PHP Official Docs](https://www.php.net/manual/)
- **MySQL:** [MySQL Official Docs](https://dev.mysql.com/doc/)
- **JavaScript:** [MDN Web Docs](https://developer.mozilla.org/)
- **HTML/CSS:** [W3Schools](https://www.w3schools.com/)

---

## 📞 Support

If you encounter issues:
1. Check the troubleshooting section above
2. Verify all files are in the correct location
3. Check browser console (F12) for errors
4. Verify database connection
5. Ensure folders have proper permissions

---

## ✨ Customization

### Change Colors
Edit `index.html` CSS variables:
```css
:root {
    --primary-black: #0a0a0a;
    --secondary-black: #1a1a1a;
    --gold: #d4af37;
    --light-gold: #f4e4a6;
    --white: #ffffff;
    --light-gray: #f5f5f5;
}
```

### Change Contact Info
Edit contact section in `index.html`:
- Email address
- Phone number
- Location
- Social media links

### Modify Song Limits
Edit `upload.php`:
```php
if ($audioFile['size'] > 100 * 1024 * 1024) { // Change 100 to your limit in MB
```

---

**Version:** 1.0  
**Last Updated:** November 28, 2025  
**License:** Free to use and modify

Happy uploading! 🎵
