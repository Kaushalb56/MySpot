<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../index.html");
    exit();
}

$restaurant_id = isset($_GET['restaurant_id']) ? (int)$_GET['restaurant_id'] : 0;
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';
$people = $_GET['people'] ?? 2;

// Fetch tables
$tables_query = $conn->query("SELECT * FROM restaurant_tables WHERE restaurant_id = $restaurant_id");
$tables = [];
while ($row = $tables_query->fetch_assoc()) {
    $tables[$row['id']] = $row;
}

// Detect unavailable tables
$unavailable = [];
$status_query = $conn->query("SELECT id FROM restaurant_tables WHERE restaurant_id = $restaurant_id AND status != 'available'");
while ($row = $status_query->fetch_assoc()) {
    $unavailable[] = $row['id'];
}
$reserved_query = $conn->query("SELECT table_id FROM reservations WHERE restaurant_id = $restaurant_id AND reservation_date = '$date' AND reservation_time = '$time'");
while ($row = $reserved_query->fetch_assoc()) {
    $unavailable[] = $row['table_id'];
}
$unavailable = array_unique($unavailable);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select a Table</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            margin: 0;
        }

        .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background-color: #ffffff;
    border-bottom: 4px solid #e0e0e0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); /* ✨ Shadow effect */
    position: sticky;
    top: 0;
    z-index: 999;
}


        .navbar .logo {
            font-weight: 600;
            font-size: 20px;
            color: #009688;
        }

        .navbar a {
            color: #333;
            margin-left: 24px;
            text-decoration: none;
        }

        .navbar .btn {
            background: #009688;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .stepper {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .stepper div {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #009688;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 20px;
        }

        .stepper div + div::before {
            content: "";
            width: 60px;
            height: 4px;
            background: #ccc;
            position: absolute;
            transform: translate(-30px, 13px);
            z-index: -1;
        }

        .summary {
            display: flex;
            gap: 20px;
            background: #f8f8f8;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            align-items: center;
            font-size: 15px;
        }

        .summary i {
            color: #009688;
            margin-right: 8px;
        }

        .title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .info-box {
            background: #e7f3ff;
            padding: 10px 16px;
            border-left: 4px solid #2196f3;
            border-radius: 6px;
            color: #31708f;
            margin-bottom: 20px;
        }
        
        .tables-area {
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
    gap: 18px;
    background: #fafafa;
}

.table-box {
    width: 90px;
    height: 90px;
    background: #ffffff;
    text-align: center;
    border: 2px solid #ccc;
    border-radius: 12px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgb(0 0 0 / 0.07);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-weight: 600;
    color: #333;
    user-select: none;
    position: relative;
}

.table-box:hover {
    box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);
    border-color: #26c6da;
    transform: translateY(-4px);
}

.table-box.unavailable {
    background-color: #e0e0e0;
    color: #999;
    cursor: not-allowed;
    border-color: #bbb;
    box-shadow: none;
    transform: none;
}

.table-box.selected {
    background-color: #8ee3ef;
    border-color: #26c6da;
    color: #004d52;
}

.table-box .seats-info {
    font-weight: normal;
    font-size: 0.85rem;
    color: #666;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.table-box .seats-info i {
    color: #009688;
    font-size: 1rem;
}


        .legend {
            margin-top: 20px;
            font-size: 14px;
        }

        .legend span {
            display: inline-block;
            margin-right: 15px;
        }

        .legend .box {
            width: 16px;
            height: 16px;
            display: inline-block;
            margin-right: 5px;
            border: 1px solid #000;
        }

        .available-box { background-color: #fff; }
        .selected-box { background-color: #8ee3ef; border-color: #26c6da; }
        .unavailable-box { background-color: #e0e0e0; }

        .btn-continue {
            background: #4db6ac;
            border: none;
            color: white;
            padding: 12px 24px;
            font-size: 16px;
            margin-top: 30px;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-continue:hover {
            background: #009688;
        }
    </style>
    <script>
        function selectTable(id) {
            const box = document.getElementById('table-' + id);
            if (box.classList.contains('unavailable')) return;

            const previouslySelected = document.querySelector('.table-box.selected');
            if (previouslySelected) {
                previouslySelected.classList.remove('selected');
            }
            box.classList.add('selected');
            document.getElementById('selected_table').value = id;
        }

        function validateForm(event) {
            if (!document.getElementById('selected_table').value) {
                alert("Please select a table before continuing.");
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
    <div class="navbar">
        <a href="customer_dashboard.php" class="logo" style="display: flex; align-items: center; text-decoration: none;">
            <img src="../images/logo-dark.svg" alt="Logo" style="height: 28px; margin-right: 10px;">
            <span style="font-weight: 600; font-size: 20px; color: #009688;">MySpot</span>
        </a>
        <div>
            <a href="restaurants.php">Find Restaurants</a>
            <a href="#"><i class="fa fa-map-marker-alt"></i> haldwani, Uttarakhand</a>
            <a href="../index.html" class="btn">Sign Out</a>
        </div>
    </div>

    <div class="container">
        <h2>Select a Table</h2>

        <div class="stepper">
            <div>1</div>
            <div style="background:#ccc">2</div>
            <div style="background:#ccc">3</div>
        </div>

        <div class="summary">
            <div><i class="fa fa-calendar-alt"></i> <?= htmlspecialchars(date("l, F j, Y", strtotime($date))) ?></div>
            <div><i class="fa fa-clock"></i> <?= htmlspecialchars(date("g:i A", strtotime($time))) ?></div>
            <div><i class="fa fa-user"></i> <?= htmlspecialchars($people) ?> people</div>
        </div>

        <div class="title">Choose your preferred table</div>

        <div class="info-box">
            <i class="fa fa-info-circle"></i>
            Tables that are unavailable are shown in gray. Click on an available table to select it.
        </div>

        <form method="post" action="preorder.php" onsubmit="validateForm(event)">
            <div class="tables-area">
                <?php foreach ($tables as $tid => $table): ?>
                    <?php
                        $shape_style = ($table['seats'] <= 2) ? 'border-radius: 50%;' : '';
                        $is_unavailable = in_array($tid, $unavailable) ? 'unavailable' : '';
                    ?>
                    <div class="table-box <?= $is_unavailable ?>" id="table-<?= $tid ?>" style="<?= $shape_style ?>" onclick="selectTable(<?= $tid ?>)">
                        <div>Table <?= htmlspecialchars($table['table_number']) ?></div>
                        <div class="seats-info">
                            <i class="fa fa-chair"></i> <?= htmlspecialchars($table['seats']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="legend">
                <span><span class="box available-box"></span> Available</span>
                <span><span class="box selected-box"></span> Selected</span>
                <span><span class="box unavailable-box"></span> Unavailable</span>
            </div>

            <input type="hidden" name="table_id" id="selected_table">
            <input type="hidden" name="restaurant_id" value="<?= $restaurant_id ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
            <input type="hidden" name="time" value="<?= htmlspecialchars($time) ?>">
            <input type="hidden" name="people" value="<?= htmlspecialchars($people) ?>">

            <button type="submit" class="btn-continue">Continue</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>