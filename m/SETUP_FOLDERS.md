# Create Upload Folders - IMPORTANT!

You MUST create the folders before uploading songs!

## Windows PowerShell Command

Open PowerShell and run this command:

```powershell
cd "c:\Users\Enockieofficial\Desktop\m" ; mkdir uploads, uploads\songs, uploads\images
```

Or run these commands one by one:

```powershell
cd "c:\Users\Enockieofficial\Desktop\m"
mkdir uploads
mkdir uploads\songs
mkdir uploads\images
```

## What This Does

Creates these folders:
- `uploads/` - Main upload folder
- `uploads/songs/` - Where audio files are stored
- `uploads/images/` - Where cover images are stored

## Verify It Worked

You should see these folders in your file explorer:
```
c:\Users\Enockieofficial\Desktop\m\
├── uploads/
│   ├── songs/
│   └── images/
```

## If Folders Already Exist

Just delete them and create fresh ones:

```powershell
rmdir uploads -Force -Recurse
mkdir uploads, uploads\songs, uploads\images
```

## Next Step

After creating folders:
1. Keep Live Server running on port 5501
2. Upload a song through the admin panel
3. It should work now!
