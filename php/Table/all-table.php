<!DOCTYPE html>
<html>

<head>
    <title>Tables - MySpot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./css/all-table.css">
    
</head>

<body>
    <!-- Left Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="../../images/MySpot_res.svg" alt="MySpot Logo" style="width: 36px; height: 36px;">
            <span>MySpot</span>
        </div>
        <div class="nav-list">
            <a href="../restaurant_dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="all-table.php" class="nav-item active">
                <i class="fas fa-chair"></i>
                <span>Tables</span>
            </a>
            <a href="../menu_manage.php" class="nav-item">
                <i class="fas fa-utensils"></i>
                <span>Menu</span>
            </a>
            <a href="../restaurant_reservations.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Reservations</span>
            </a>
            <a href="../manager_profile.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Profile</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <button class="toggle-btn" id="toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-avatar">
                    <span>M</span>
                </div>
                <span style="margin-left: 10px;">Manager</span>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content">
            <div class="page-header">
                <div>
                    <h1>Tables</h1>
                    <p class="page-description">Manage restaurant tables and seating</p>
                </div>
                <button class="add-table-btn" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Table
                </button>
            </div>

            <div class="status-counts">
                <div class="status-badge status-available">
                    <span id="available-count">6 </span>  Available
                </div>
                <div class="status-badge status-reserved">
                    <span id="reserved-count">2 </span>  Reserved
                </div>
                <div class="status-badge status-occupied">
                    <span id="occupied-count">2 </span>  Unavailable
                </div>
            </div>

            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Search tables..." id="searchInput">
                <button class="filter-btn">
                    <i class="fas fa-filter"></i>
                </button>
            </div>

            <div class="tables-grid" id="tablesGrid">
                <!-- Table cards will be generated here by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Add/Edit Table Modal -->
    <div class="modal" id="tableModal">
        <div class="modal-content">
            <h2 id="modalTitle">Add New Table</h2>
            <form id="tableForm">
                <input type="hidden" id="tableId" name="table_id">
                <div class="form-group">
                    <label for="tableNumber">Table Number</label>
                    <input type="number" id="tableNumber" name="table_number" required>
                </div>
                <div class="form-group">
                    <label for="tableSeats">Number of Seats</label>
                    <input type="number" id="tableSeats" name="seats" required>
                </div>
                <div class="form-group">
                    <label for="tableStatus">Status</label>
                    <select id="tableStatus" name="status">
                        <option value="0">Available</option>
                        <option value="1">Unavailable</option>
                        <option value="2">Reserved</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tablePrice">Price per Seat (Optional)</label>
                    <input type="number" id="tablePrice" name="price" step="0.01">
                </div>
                <div class="button-group">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete this table? This action cannot be undone.</p>
            <div class="button-group">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn-save" style="background-color: #f44336;"
                    onclick="confirmDelete()">Delete</button>
            </div>
            <input type="hidden" id="deleteTableId">
        </div>
    </div>

    <!-- Success Toast -->
    <div class="toast" id="successToast"></div>

    <script src="./js/table.js"></script>
    
</body>

</html>
<script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('expanded');
        });
        
        function checkWidth() {
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('expanded');
            } else {
                sidebar.classList.add('expanded');
            }
        }
        checkWidth();
        window.addEventListener('resize', checkWidth);
    </script>