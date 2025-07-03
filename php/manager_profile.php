<?php
session_start();
include 'db.php';

$restaurant_id = $_SESSION['restaurant_id'];

// Fetch restaurant details
$sql = "SELECT * FROM restaurants WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();
$restaurant = $result->fetch_assoc();

$restaurant_name = $restaurant['name'];
$restaurant_initial = strtoupper(substr($restaurant_name, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Profile - <?php echo htmlspecialchars($restaurant_name); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8e2441;
            --primary-light: #a53555;
            --primary-dark: #6d1c32;
            --secondary-color: #f8f9fa;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
            --shadow-light: 0 2px 8px rgba(0,0,0,0.06);
            --shadow-medium: 0 4px 20px rgba(0,0,0,0.08);
            --shadow-heavy: 0 8px 40px rgba(0,0,0,0.12);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        /* Desktop-First Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: var(--shadow-medium);
            z-index: 100;
            border-right: 1px solid var(--border-color);
        }

        .logo {
            padding: 30px 24px;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 22px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
        }

        .logo img {
            width: 36px;
            height: 36px;
            margin-right: 14px;
        }

        .nav-list {
            list-style: none;
            padding: 24px 0;
        }

        .nav-item {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition);
            margin: 3px 16px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 15px;
            position: relative;
        }

        .nav-item:hover {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
            color: var(--primary-color);
            transform: translateX(6px);
            text-decoration: none;
            box-shadow: var(--shadow-light);
        }

        .nav-item.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            box-shadow: var(--shadow-medium);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -16px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 32px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .nav-item i {
            width: 22px;
            text-align: center;
            margin-right: 14px;
            font-size: 17px;
        }

        /* Desktop Main Content */
        .main-content {
            margin-left: 280px;
            padding: 40px 48px;
            min-height: 100vh;
            max-width: calc(100vw - 280px);
        }

        /* Desktop Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 28px 40px;
            border-radius: var(--border-radius);
            margin-bottom: 40px;
            box-shadow: var(--shadow-heavy);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="40" r="1" fill="rgba(255,255,255,0.08)"/><circle cx="80" cy="30" r="1.2" fill="rgba(255,255,255,0.06)"/></svg>');
            pointer-events: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .user-avatar {
            background: rgba(255,255,255,0.2);
            color: white;
            border-radius: 50%;
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 18px;
            border: 3px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
            font-size: 20px;
        }

        .user-details h3 {
            font-weight: 600;
            font-size: 20px;
            margin: 0;
        }

        .user-details p {
            opacity: 0.85;
            font-size: 14px;
            margin: 0;
            font-weight: 400;
        }

        /* Desktop Profile Container */
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-heavy);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .profile-header {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f4f8 100%);
            padding: 50px 48px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .profile-header h2 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 12px;
        }

        .profile-header p {
            color: var(--text-muted);
            font-size: 16px;
            margin: 0;
        }

        .profile-body {
            padding: 50px 48px;
        }

        /* Desktop Form Layout */
        .form-row {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 48px;
            align-items: start;
        }

        .image-section {
            text-align: center;
        }

        .restaurant-img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 20px;
            border: 4px solid var(--border-color);
            box-shadow: var(--shadow-medium);
            transition: var(--transition);
            margin-bottom: 24px;
        }

        .restaurant-img:hover {
            transform: scale(1.03);
            box-shadow: var(--shadow-heavy);
        }

        .form-fields {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
            display: block;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 16px;
            transition: var(--transition);
            background: #fafbfc;
            font-weight: 500;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(142, 36, 65, 0.1);
            background: white;
            outline: none;
        }

        .form-control:hover {
            border-color: var(--primary-light);
            background: white;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        /* Time Inputs - Side by Side */
        .time-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        /* File Input Enhancement */
        .file-input-wrapper {
            width: 100%;
            margin-top: 20px;
        }

        .file-input-custom {
            display: none;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px 20px;
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            background: #fafbfc;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            text-align: center;
        }

        .file-input-label:hover {
            border-color: var(--primary-color);
            background: rgba(142, 36, 65, 0.02);
            color: var(--primary-color);
        }

        .file-input-label i {
            margin-right: 8px;
        }

        /* Save Button */
        .save-button-container {
            margin-top: 48px;
            text-align: center;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: none;
            color: white;
            padding: 18px 48px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
            box-shadow: var(--shadow-medium);
            position: relative;
            overflow: hidden;
            min-width: 200px;
        }

        .btn-primary-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
        }

        .btn-primary-custom:hover::before {
            left: 100%;
        }

        .btn-primary-custom:active {
            transform: translateY(-1px);
        }

        /* Loading Animation */
        .btn-primary-custom.loading {
            pointer-events: none;
            position: relative;
        }

        .btn-primary-custom.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }

        /* Success Message */
        .alert-success-custom {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 1px solid #b8dacc;
            color: #155724;
            padding: 18px 24px;
            border-radius: 12px;
            margin-bottom: 32px;
            font-weight: 500;
        }

        /* Header Icon */
        .header-icon {
            position: relative;
            z-index: 1;
            font-size: 28px;
            opacity: 0.8;
        }

        /* Form Icons */
        .form-label i {
            margin-right: 8px;
            color: var(--primary-color);
            width: 16px;
        }

        /* Responsive adjustments for very large screens */
        @media (min-width: 1400px) {
            .main-content {
                padding: 48px 64px;
            }
            
            .profile-container {
                max-width: 1200px;
            }
            
            .form-row {
                grid-template-columns: 350px 1fr;
                gap: 64px;
            }
            
            .restaurant-img {
                width: 260px;
                height: 260px;
            }
        }

        /* Tablet and below - make it work but not mobile-first */
        @media (max-width: 1024px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar .logo span,
            .sidebar .nav-item span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
                padding: 32px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 32px;
            }
            
            .restaurant-img {
                width: 180px;
                height: 180px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../images/MySpot_res.svg" alt="MySpot Logo">
            <span>MySpot</span>
        </div>
        <div class="nav-list">
            <a href="restaurant_dashboard.php" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="./Table/all-table.php" class="nav-item">
                <i class="fas fa-chair"></i>
                <span>Tables</span>
            </a>
            <a href="menu_manage.php" class="nav-item">
                <i class="fas fa-utensils"></i>
                <span>Menu</span>
            </a>
            <a href="restaurant_reservations.php" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span>Reservations</span>
            </a>
            <a href="manager_profile.php" class="nav-item active">
                <i class="fas fa-user-cog"></i>
                <span>Profile</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="user-info">
                <div class="user-avatar"><?php echo $restaurant_initial; ?></div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($restaurant_name); ?></h3>
                    <p>Restaurant Manager Dashboard</p>
                </div>
            </div>
            <div class="header-icon">
                <i class="fas fa-store-alt"></i>
            </div>
        </div>

        <div class="profile-container">
            <div class="profile-header">
                <h2><i class="fas fa-store-alt" style="margin-right: 16px;"></i>Restaurant Profile</h2>
                <p>Manage your restaurant information and settings</p>
            </div>
            
            <div class="profile-body">
                <form action="update_profile.php" method="POST" enctype="multipart/form-data" id="profileForm">
                    <div class="form-row">
                        <!-- Left Column - Image -->
                        <div class="image-section">
                            <?php if (!empty($restaurant['image'])): ?>
                                <img src="<?php echo $restaurant['image']; ?>" alt="Restaurant Image" class="restaurant-img" id="imagePreview">
                            <?php else: ?>
                                <img src="../images/restaurant_bg.jpg" alt="Default Image" class="restaurant-img" id="imagePreview">
                            <?php endif; ?>
                            
                            <div class="file-input-wrapper">
                                <input type="file" name="image" class="file-input-custom" id="imageInput" accept="image/*">
                                <label for="imageInput" class="file-input-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    Upload New Photo
                                </label>
                            </div>
                        </div>

                        <!-- Right Column - Form Fields -->
                        <div class="form-fields">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-store"></i> Restaurant Name
                                </label>
                                <input type="text" name="name" id="name" class="form-control" 
                                       value="<?php echo htmlspecialchars($restaurant['name']); ?>" 
                                       required placeholder="Enter restaurant name">
                            </div>

                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea name="description" id="description" class="form-control" 
                                          placeholder="Tell customers about your restaurant, cuisine type, ambiance..."><?php echo htmlspecialchars($restaurant['description']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="state_location" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Location
                                </label>
                                <input type="text" name="state_location" id="state_location" class="form-control" 
                                       value="<?php echo htmlspecialchars($restaurant['state_location']); ?>" 
                                       placeholder="City, State">
                            </div>

                            <div class="time-row">
                                <div class="form-group">
                                    <label for="opening_time" class="form-label">
                                        <i class="fas fa-clock"></i> Opening Time
                                    </label>
                                    <input type="time" name="opening_time" id="opening_time" class="form-control" 
                                           value="<?php echo $restaurant['opening_time']; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="closing_time" class="form-label">
                                        <i class="fas fa-clock"></i> Closing Time
                                    </label>
                                    <input type="time" name="closing_time" id="closing_time" class="form-control" 
                                           value="<?php echo $restaurant['closing_time']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="save-button-container">
                        <button type="submit" class="btn btn-primary-custom" id="saveBtn">
                            <i class="fas fa-save" style="margin-right: 10px;"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const fileInputLabel = document.querySelector('.file-input-label');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        imagePreview.style.transform = 'scale(1)';
                    }, 300);
                };
                reader.readAsDataURL(file);
                
                fileInputLabel.innerHTML = `<i class="fas fa-check"></i> ${file.name}`;
                fileInputLabel.style.borderColor = 'var(--primary-color)';
                fileInputLabel.style.background = 'rgba(142, 36, 65, 0.05)';
                fileInputLabel.style.color = 'var(--primary-color)';
            }
        });

        // Drag and drop functionality
        const dropArea = fileInputLabel;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropArea.style.borderColor = 'var(--primary-color)';
            dropArea.style.background = 'rgba(142, 36, 65, 0.1)';
        }

        function unhighlight(e) {
            dropArea.style.borderColor = 'var(--border-color)';
            dropArea.style.background = '#fafbfc';
        }

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                imageInput.files = files;
                const event = new Event('change', { bubbles: true });
                imageInput.dispatchEvent(event);
            }
        }

        // Form submission with loading state
        const form = document.getElementById('profileForm');
        const saveBtn = document.getElementById('saveBtn');

        form.addEventListener('submit', function(e) {
            saveBtn.classList.add('loading');
            saveBtn.innerHTML = '<i class="fas fa-save" style="margin-right: 10px;"></i>Saving Changes...';
        });

        // Form validation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = 'var(--border-color)';
                }
            });
        });

        // Success animation for form submission
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === '1') {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert-success-custom';
                alertDiv.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 10px;"></i>Profile updated successfully!';
                
                const profileBody = document.querySelector('.profile-body');
                profileBody.insertBefore(alertDiv, profileBody.firstChild);
                
                setTimeout(() => {
                    alertDiv.style.transition = 'all 0.5s ease';
                    alertDiv.style.opacity = '0';
                    alertDiv.style.transform = 'translateY(-20px)';
                    setTimeout(() => alertDiv.remove(), 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>