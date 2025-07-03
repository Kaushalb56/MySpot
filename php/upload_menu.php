
<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex justify-center items-center">

<div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-lg">
    <h2 class="text-2xl font-bold text-center text-maroon mb-6">Add New Menu Item</h2>

    <form action="menu_handler.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Dish Name</label>
            <input type="text" name="name" required class="w-full border rounded-lg px-4 py-2">
        </div>

        <div>
            <label class="block mb-1 font-medium">Description</label>
            <textarea name="description" required class="w-full border rounded-lg px-4 py-2"></textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">Price (Rs.)</label>
            <input type="number" name="price" required step="0.01" class="w-full border rounded-lg px-4 py-2">
        </div>

        <div>
            <label class="block mb-1 font-medium">Category</label>
            <select name="category" required class="w-full border rounded-lg px-4 py-2">
              <option value="starter">Starters</option>
              <option value="main_course">Main Courses</option>
              <option value="dessert">Desserts</option>
            <option value="drink">Drinks</option>
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Image</label>
            <input type="file" name="image" required class="w-full">
        </div>

        <div class="text-center pt-4">
            <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white font-bold px-6 py-2 rounded-lg">
                Add Item
            </button>
        </div>
    </form>
</div>

<style>
    .bg-maroon { background-color: #77243A; }
    .hover\:bg-maroon-dark:hover { background-color: #5a1b2b; }
</style>

</body>
</html>
