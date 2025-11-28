// Mobile Menu Toggle
document.querySelector('.mobile-menu').addEventListener('click', function() {
    const navUl = document.querySelector('nav ul');
    navUl.style.display = navUl.style.display === 'flex' ? 'none' : 'flex';
});

// Audio Player Functionality
const audioPlayer = document.getElementById('audio-player');
const playPauseBtn = document.getElementById('play-pause-btn');
const playPauseIcon = document.getElementById('play-pause-icon');
const progressBar = document.getElementById('progress-bar');
const progress = document.getElementById('progress');
const currentTimeEl = document.getElementById('current-time');
const durationEl = document.getElementById('duration');
const currentSongTitle = document.getElementById('current-song-title');
const currentSongImg = document.getElementById('current-song-img');
const closePlayerBtn = document.getElementById('close-player');

let isPlaying = false;
let currentAudio = null;
let currentSongId = null;

// Load songs from database on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSongsFromDatabase();
});

// Load songs from database
function loadSongsFromDatabase(filter = 'all') {
    fetch(`api_songs.php?action=list&filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displaySongs(data.songs);
            } else {
                console.error('Error loading songs:', data.message);
            }
        })
        .catch(error => console.error('Fetch error:', error));
}

// Display songs in the grid
function displaySongs(songs) {
    const songsGrid = document.querySelector('.songs-grid');
    songsGrid.innerHTML = '';
    
    if (songs.length === 0) {
        songsGrid.innerHTML = '<p style="text-align: center; color: #ccc;">No songs available</p>';
        return;
    }
    
    songs.forEach(song => {
        const songCard = document.createElement('div');
        songCard.className = 'song-card fade-in';
        
        const imageUrl = song.image_path ? song.image_path : 'https://via.placeholder.com/300x200?text=No+Image';
        const downloads = song.downloads || 0;
        
        songCard.innerHTML = `
            <div class="song-image">
                <img src="${imageUrl}" alt="${song.title}">
            </div>
            <div class="song-info">
                <h3 class="song-title">${song.title}</h3>
                <p class="song-description">${song.description || 'No description'}</p>
                <div class="song-controls">
                    <button class="play-btn" data-song-id="${song.id}" data-song="${song.title}" data-image="${imageUrl}" data-file="${song.file_path}">
                        <i class="fas fa-play"></i>
                    </button>
                    <button class="download-btn" data-song-id="${song.id}">
                        <i class="fas fa-download"></i>
                    </button>
                    <span class="download-stats">${downloads} Downloads</span>
                </div>
            </div>
        `;
        
        songsGrid.appendChild(songCard);
    });
    
    // Add event listeners to new buttons
    attachSongEventListeners();
}

// Attach event listeners to dynamically created buttons
function attachSongEventListeners() {
    // Play buttons
    document.querySelectorAll('.play-btn').forEach(button => {
        button.addEventListener('click', function() {
            const songId = this.getAttribute('data-song-id');
            const songTitle = this.getAttribute('data-song');
            const songImage = this.getAttribute('data-image');
            const songFile = this.getAttribute('data-file');
            
            playSong(songId, songTitle, songImage, songFile);
        });
    });
    
    // Download buttons
    document.querySelectorAll('.download-btn').forEach(button => {
        button.addEventListener('click', function() {
            const songId = this.getAttribute('data-song-id');
            downloadSong(songId);
        });
    });
}

// Play a song
function playSong(songId, songTitle, songImage, songFile) {
    // If another song is playing, stop it
    if (currentAudio) {
        currentAudio.pause();
        currentAudio = null;
    }
    
    // Create new audio element using the actual file path
    currentSongId = songId;
    currentAudio = new Audio(songFile);
    currentAudio.crossOrigin = "anonymous";
    
    // Update player UI
    currentSongTitle.textContent = songTitle;
    currentSongImg.src = songImage;
    
    // Show audio player
    audioPlayer.style.display = 'block';
    
    // Play the song
    currentAudio.play().catch(error => {
        console.error('Play error:', error);
        alert('Failed to play song. File may not exist.');
    });
    isPlaying = true;
    playPauseIcon.className = 'fas fa-pause';
    
    // Update progress bar and time
    currentAudio.addEventListener('timeupdate', function() {
        const percent = (currentAudio.currentTime / currentAudio.duration) * 100;
        progress.style.width = `${percent}%`;
        
        const currentMins = Math.floor(currentAudio.currentTime / 60);
        const currentSecs = Math.floor(currentAudio.currentTime % 60);
        currentTimeEl.textContent = `${currentMins}:${currentSecs < 10 ? '0' : ''}${currentSecs}`;
    });
    
    currentAudio.addEventListener('loadedmetadata', function() {
        const totalMins = Math.floor(currentAudio.duration / 60);
        const totalSecs = Math.floor(currentAudio.duration % 60);
        durationEl.textContent = `${totalMins}:${totalSecs < 10 ? '0' : ''}${totalSecs}`;
    });
    
    currentAudio.addEventListener('ended', function() {
        playNextSong();
    });
}

// Download a song
function downloadSong(songId) {
    fetch('direct_download.php?id=' + songId)
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Download failed');
        })
        .then(blob => {
            // Create a download link
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `song_${songId}.mp3`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Download error:', error);
            alert('Failed to download song');
        });
}

// Play/Pause button
playPauseBtn.addEventListener('click', function() {
    if (!currentAudio) return;
    
    if (isPlaying) {
        currentAudio.pause();
        playPauseIcon.className = 'fas fa-play';
        isPlaying = false;
    } else {
        currentAudio.play();
        playPauseIcon.className = 'fas fa-pause';
        isPlaying = true;
    }
});

// Close player
closePlayerBtn.addEventListener('click', function() {
    if (currentAudio) {
        currentAudio.pause();
        currentAudio = null;
    }
    audioPlayer.style.display = 'none';
    isPlaying = false;
    playPauseIcon.className = 'fas fa-play';
    progress.style.width = '0%';
    currentTimeEl.textContent = '0:00';
    currentSongId = null;
});

// Progress bar click
progressBar.addEventListener('click', function(e) {
    if (!currentAudio) return;
    const percent = e.offsetX / this.offsetWidth;
    currentAudio.currentTime = percent * currentAudio.duration;
});

// Next button (placeholder - would need a queue system)
document.getElementById('next-btn').addEventListener('click', function() {
    if (currentAudio) {
        playNextSong();
    }
});

// Previous button (placeholder)
document.getElementById('prev-btn').addEventListener('click', function() {
    if (currentAudio) {
        currentAudio.currentTime = 0;
    }
});

function playNextSong() {
    // This is a placeholder - implement proper queue logic if needed
    console.log('Next song');
}

// Contact form submission
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        subject: document.getElementById('subject').value,
        message: document.getElementById('message').value
    };
    
    alert('Thank you for your message! Enockie will get back to you soon.');
    this.reset();
});

// Newsletter form submission
document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;
    alert('Thank you for subscribing! Check your email at ' + email);
    this.reset();
});

// Music filter buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Load songs based on filter
        const filter = this.textContent.toLowerCase().replace(/[^a-z]/g, '');
        const filterType = filter === 'allsongs' ? 'all' : (filter.includes('latest') ? 'latest' : 'popular');
        loadSongsFromDatabase(filterType);
    });
});

// Admin Dashboard Functionality
const adminSection = document.getElementById('admin-section');
const loginModal = document.getElementById('login-modal');
const adminLoginBtn = document.getElementById('admin-login');
const closeLoginBtn = document.getElementById('close-login');
const loginForm = document.getElementById('login-form');
const logoutBtn = document.getElementById('logout-btn');
const adminNavBtns = document.querySelectorAll('.admin-nav-btn');
const adminPanels = document.querySelectorAll('.admin-panel');

// Show login modal
adminLoginBtn.addEventListener('click', function(e) {
    e.preventDefault();
    loginModal.style.display = 'flex';
});

// Close login modal
closeLoginBtn.addEventListener('click', function() {
    loginModal.style.display = 'none';
});

// Login form submission
loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    // Simple authentication (in a real app, this would be server-side)
    if (username === 'admin' && password === 'password') {
        // Hide main content and show admin dashboard
        document.querySelectorAll('section:not(#admin-section), footer').forEach(el => {
            el.style.display = 'none';
        });
        adminSection.style.display = 'block';
        loginModal.style.display = 'none';
        
        // Load admin data
        loadAdminStats();
        loadAdminSongs();
        
        // Reset form
        this.reset();
    } else {
        alert('Invalid username or password.\n\nCredentials:\nUsername: admin\nPassword: password');
    }
});

// Logout functionality
logoutBtn.addEventListener('click', function() {
    // Show main content and hide admin dashboard
    document.querySelectorAll('section:not(#admin-section), footer').forEach(el => {
        el.style.display = 'block';
    });
    adminSection.style.display = 'none';
});

// Admin navigation
adminNavBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons and panels
        adminNavBtns.forEach(b => b.classList.remove('active'));
        adminPanels.forEach(p => p.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Show corresponding panel
        const panelId = this.getAttribute('data-panel') + '-panel';
        document.getElementById(panelId).classList.add('active');
    });
});

// Load admin stats
function loadAdminStats() {
    fetch('api_songs.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const songs = data.songs;
                
                // Calculate stats
                const totalSongs = songs.length;
                const totalDownloads = songs.reduce((sum, song) => sum + (song.downloads || 0), 0);
                const mostDownloaded = songs.length > 0 ? Math.max(...songs.map(s => s.downloads || 0)) : 0;
                
                // Update dashboard stats
                const statCards = document.querySelectorAll('.stat-card');
                if (statCards.length >= 4) {
                    statCards[0].querySelector('.stat-value').textContent = totalSongs;
                    statCards[1].querySelector('.stat-value').textContent = totalDownloads.toLocaleString();
                    statCards[2].querySelector('.stat-value').textContent = '~' + Math.round(totalSongs * 270); // Estimated listeners
                    statCards[3].querySelector('.stat-value').textContent = Math.round(totalSongs * 102); // Estimated subscribers
                }
            }
        })
        .catch(error => console.error('Error loading admin stats:', error));
}

// Load admin songs
function loadAdminSongs() {
    fetch('api_songs.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayAdminSongs(data.songs);
            }
        })
        .catch(error => console.error('Error loading admin songs:', error));
}

// Display admin songs
function displayAdminSongs(songs) {
    const tbody = document.querySelector('#songs-panel .admin-table tbody');
    tbody.innerHTML = '';
    
    if (songs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #999;">No songs uploaded yet</td></tr>';
    } else {
        songs.forEach(song => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${song.title}</td>
                <td>${song.description || 'No description'}</td>
                <td>${song.downloads || 0}</td>
                <td>${new Date(song.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="action-btn edit-btn" data-song-id="${song.id}"><i class="fas fa-edit"></i></button>
                    <button class="action-btn delete-btn" data-song-id="${song.id}"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    
    // Update stats panel with real data
    updateStatsPanel(songs);
    
    // Add event listeners
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const songId = this.getAttribute('data-song-id');
            editSong(songId);
        });
    });
    
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const songId = this.getAttribute('data-song-id');
            if (confirm('Are you sure you want to delete this song?')) {
                deleteSongAdmin(songId);
            }
        });
    });
}

// Update stats panel with real song data
function updateStatsPanel(songs) {
    const statsTable = document.querySelector('#stats-panel .admin-table tbody');
    const popularSongsGrid = document.querySelector('#stats-panel .stats-grid');
    
    if (statsTable) {
        statsTable.innerHTML = '';
        
        if (songs.length === 0) {
            statsTable.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #999;">No statistics available yet</td></tr>';
        } else {
            // Sort by downloads
            const sortedSongs = [...songs].sort((a, b) => (b.downloads || 0) - (a.downloads || 0));
            
            sortedSongs.forEach(song => {
                const row = document.createElement('tr');
                const thisMonth = Math.round((song.downloads || 0) * 0.6); // Estimate
                const lastMonth = Math.round((song.downloads || 0) * 0.4); // Estimate
                
                row.innerHTML = `
                    <td>${song.title}</td>
                    <td>${song.downloads || 0}</td>
                    <td>${thisMonth}</td>
                    <td>${lastMonth}</td>
                `;
                statsTable.appendChild(row);
            });
        }
    }
    
    // Update popular songs
    if (popularSongsGrid) {
        const sortedSongs = [...songs].sort((a, b) => (b.downloads || 0) - (a.downloads || 0));
        const topThree = sortedSongs.slice(0, 3);
        
        popularSongsGrid.innerHTML = '';
        
        if (topThree.length === 0) {
            popularSongsGrid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #999; padding: 20px;">No songs yet</div>';
        } else {
            topThree.forEach((song, index) => {
                const card = document.createElement('div');
                card.className = 'stat-card';
                card.innerHTML = `
                    <div class="stat-value">#${index + 1}</div>
                    <div class="stat-label">${song.title}</div>
                    <div style="font-size: 12px; color: #999; margin-top: 8px;">${song.downloads || 0} downloads</div>
                `;
                popularSongsGrid.appendChild(card);
            });
        }
    }
}

// Delete song from admin
function deleteSongAdmin(songId) {
    fetch('api_songs.php?action=delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'song_id=' + songId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Song deleted successfully!');
            loadAdminSongs();
            loadSongsFromDatabase();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        alert('Failed to delete song');
    });
}

// Edit song
function editSong(songId) {
    alert('Edit song functionality - Song ID: ' + songId);
    // Implement edit form here
}

// Upload form submission
document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Song uploaded successfully!');
            this.reset();
            loadAdminSongs();
            loadSongsFromDatabase();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Failed to upload song');
    });
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
            
            // Close mobile menu if open
            if (window.innerWidth <= 768) {
                const navUl = document.querySelector('nav ul');
                navUl.style.display = 'none';
            }
        }
    });
});

// Fade in animation on scroll
const fadeElements = document.querySelectorAll('.fade-in');

const fadeInOnScroll = function() {
    fadeElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('fade-in');
        }
    });
};

window.addEventListener('scroll', fadeInOnScroll);
// Initial check
fadeInOnScroll();
