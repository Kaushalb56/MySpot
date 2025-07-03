<?php
session_start();
include 'db.php';

$restaurant_id = $_SESSION['restaurant_id'];

// Fetch restaurant name
$sql_restaurant = "SELECT name FROM restaurants WHERE id = ?";
$stmt_rest = $conn->prepare($sql_restaurant);
$stmt_rest->bind_param("i", $restaurant_id);
$stmt_rest->execute();
$result_rest = $stmt_rest->get_result();
$restaurant = $result_rest->fetch_assoc();

$restaurant_name = $restaurant['name'];
$restaurant_initial = strtoupper(substr($restaurant_name, 0, 1));

// Fetch menu items grouped by category
$sql = "SELECT * FROM menu_items WHERE restaurant_id = $restaurant_id ORDER BY category, name";
$result = $conn->query($sql);

$menu_items = [];
while ($row = $result->fetch_assoc()) {
    $menu_items[$row['category']][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <style>
        body {
            background-color: #f9f9f9;
        }
        .sidebar {
            width: 250px;
            background-color: white;
            height: 100vh;
            position: fixed;
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            z-index: 100;
            overflow-x: hidden;
            left: 0;
        }
        
        .sidebar.expanded {
            width: 250px;
        }
        
        .logo {
            padding: 20px;
            color: #8e2441;
            font-weight: bold;
            font-size: 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f0f0f0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            justify-content: center;
        }
        
        .sidebar.expanded .logo {
            justify-content: flex-start;
        }
        
        .nav-list {
            list-style: none;
            padding: 0;
        }
        
        .nav-item {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            color: #666;
            text-decoration: none;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .nav-item span {
            margin-left: 10px;
            opacity: 0;
            display: none;
            transition: all 0.3s;
        }
        
        .sidebar.expanded .nav-item span {
            opacity: 1;
            display: inline;
        }
        
        .nav-item:hover {
            background-color: #f5f5f5;
            color: #8e2441;
        }
        
        .nav-item.active {
            background-color: #f5f5f5;
            color: #8e2441;
            border-left: 4px solid #8e2441;
            font-weight: bold;
        }
        
        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 60px;
            transition: all 0.3s;
            padding: 20px;
            width: calc(100% - 60px);
        }
        
        .sidebar.expanded ~ .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #8e2441;
            padding: 15px 20px;
            color: white;
            margin: -20px -20px 20px -20px;
        }
        
        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            display: none;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
            font-weight: bold;
        }
        
        .notification-icon {
            margin-right: 20px;
            position: relative;
            cursor: pointer;
        }
        .content {
            
            padding: 30px;
        }
        .category-section {
            margin-bottom: 40px;
        }
        .menu-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 16px;
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        .menu-card img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 16px;
        }
        .menu-card .info {
            flex-grow: 1;
        }
        .menu-card .actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .form-switch .form-check-input {
            cursor: pointer;
        }
        .edit-form {
            display: none;
            width: 100%;
            margin-top: 10px;
        }
        @media (max-width: 992px) {
            .sidebar {
                width: 0;
            }
            
            .sidebar.expanded {
                width: 250px;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .toggle-btn {
                display: block;
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="../images/MySpot_res.svg" alt="MySpot Logo" style="width: 36px; height: 36px;">
            <span>MySpot</span>
        </div>
        <div class="nav-list">
            <a href="restaurant_dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="./Table/all-table.php" class="nav-item">
                <i class="fas fa-chair"></i>
                <span>Tables</span>
            </a>
            <a href="menu_manage.php" class="nav-item active">
                <i class="fas fa-utensils"></i>
                <span>Menu</span>
            </a>
            <a href="restaurant_reservations.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Reservations</span>
            </a>
            <a href="manager_profile.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Profile</span>
            </a>
        </div>
    </div>
    
    <div class="main-content" id="main-content">
        <div class="header">
            <button class="toggle-btn" id="toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info">
    <div class="notification-icon">
        <i class="fas fa-bell"></i>
    </div>
    <a href="manager_profile.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
        <div class="user-avatar"><span><?php echo $restaurant_initial; ?></span></div>
        <span style="margin-left: 10px;"><?php echo htmlspecialchars($restaurant_name); ?></span>
    </a>
</div>

        </div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Menu</h2>
<button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#addMenuModal">➕ Add Menu Item</button>

    </div>

    <?php foreach ($menu_items as $category => $items): ?>
        <div class="category-section">
            <h5 class="bg-maroon text-white py-2 px-3 rounded"><?php echo htmlspecialchars($category); ?></h5>

            <?php foreach ($items as $item): ?>
                <div class="menu-card">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Dish">
                    <div class="info">
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($item['description']); ?></small><br>
                        <span class="text-success">Rs.<?php echo number_format($item['price'], 2); ?></span>
                    </div>
                    <div class="actions">
                        <label class="form-check-label">Available</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input toggle-availability" type="checkbox" data-id="<?php echo $item['id']; ?>" <?php echo $item['is_available'] ? 'checked' : ''; ?>>
                        </div>
                        <button class="btn btn-light border edit-btn" data-id="<?php echo $item['id']; ?>">✏️</button>
                    </div>
                    <form class="edit-form" id="edit-form-<?php echo $item['id']; ?>" method="POST" action="edit_menu_item.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <div class="row g-2 mt-2">
                            <div class="col-md-3">
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($item['name']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="price" step="0.01" class="form-control" value="<?php echo $item['price']; ?>" required>
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-control">
                                  <option value="starter" <?php if ($item['category'] === 'starter') echo 'selected'; ?>>Starters</option>
                                  <option value="main_course" <?php if ($item['category'] === 'main_course') echo 'selected'; ?>>Main Courses</option>
                                  <option value="dessert" <?php if ($item['category'] === 'dessert') echo 'selected'; ?>>Desserts</option>
                                  <option value="drink" <?php if ($item['category'] === 'drink') echo 'selected'; ?>>Drinks</option>
                                </select>

                            </div>
                            <div class="col-md-2">
                                <input type="file" name="image" class="form-control">
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-success">💾 Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
<!-- Modal: Add Menu Item -->
<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-slideout">
    <div class="modal-content rounded-3">
      <div class="modal-header bg-maroon text-white">
        <h5 class="modal-title" id="addMenuModalLabel">Add New Menu Item</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="menu_handler.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Dish Name</label>
              <input type="text" name="name" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Category</label>
              <select name="category" required class="form-control">
                <option value="starter">Starters</option>
                <option value="main_course">Main Courses</option>
                <option value="dessert">Desserts</option>
                <option value="drink">Drinks</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" required class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Price (Rs.)</label>
              <input type="number" name="price" step="0.01" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Image</label>
              <input type="file" name="image" required class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-maroon">➕ Add Item</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $('.toggle-availability').on('change', function () {
        const menuId = $(this).data('id');
        const isAvailable = $(this).is(':checked') ? 1 : 0;

        $.post('toggle_availability.php', {
            menu_item_id: menuId,
            is_available: isAvailable
        }, function (response) {
            console.log('Updated:', response);
        });
    });

    $('.edit-btn').on('click', function () {
        const id = $(this).data('id');
        $(`#edit-form-${id}`).slideToggle();
    });
     const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');

    toggleBtn.addEventListener('click', () => {
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

<style>
    .btn-maroon {
    background-color: #8e2441;
    color: white;
    transition: background-color 0.3s ease;
}

.btn-maroon:hover {
    background-color: #6c1a32; /* Darker maroon */
    color: white;
}

    .bg-maroon {
        background-color: #8e2441;
    }
    .modal-dialog-slideout {
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    margin: 0;
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    width: 500px;
    max-width: 90%;
  }
  .modal.fade .modal-dialog-slideout {
    transform: translateX(100%);
  }
  .modal.show .modal-dialog-slideout {
    transform: translateX(0);
  }
  
</style>

</body>
</html>
