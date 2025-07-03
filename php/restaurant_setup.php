<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .bg-maroon { background-color: #77243A; }
        .hover\:bg-maroon-dark:hover { background-color: #5a1b2b; }
        .text-maroon { color: #77243A; }
        .transition-base { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="min-h-screen bg-cover bg-center bg-no-repeat flex items-center justify-center p-4 relative"
      style="background-image: url('../images/restaurant_bg.jpg');">

    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-60 z-0"></div>

    <!-- Form container -->
    <div class="bg-white border border-gray-200 shadow-2xl rounded-3xl p-10 w-full max-w-3xl z-10 relative">
        <h2 class="text-3xl font-bold mb-8 text-center text-maroon">🍽️ Setup Your Restaurant</h2>

        <form action="setup_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">Restaurant Image</label>
                <input type="file" name="image" required class="w-full border border-gray-300 rounded-xl px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-maroon">
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">Cuisine</label>
                <input type="text" name="cuisine" required class="w-full border border-gray-300 rounded-xl px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-maroon">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Opening Time</label>
                    <input type="time" name="opening_time" required class="w-full border border-gray-300 rounded-xl px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-maroon">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Closing Time</label>
                    <input type="time" name="closing_time" required class="w-full border border-gray-300 rounded-xl px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-maroon">
                </div>
            </div>

            <div>
                <h4 class="text-lg font-semibold text-maroon mb-2">🪑 Table Setup</h4>
                <div id="tables" class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-700 font-medium">Table 1</span>
                        <input type="number" name="seats[]" placeholder="Seats" required class="flex-1 border border-gray-300 rounded-xl px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-maroon">
                    </div>
                </div>
                <button type="button" onclick="addTable()" class="mt-4 inline-flex items-center gap-1 text-sm bg-maroon text-white px-4 py-2 rounded-xl transition-base hover:bg-maroon-dark">
                    ➕ Add Table
                </button>
            </div>

            <div class="text-center pt-4">
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white font-bold text-lg px-6 py-3 rounded-xl transition-base">
                    💾 Save Info
                </button>
            </div>
        </form>
    </div>

    <script>
        let tableCount = 1;
        function addTable() {
            tableCount++;
            const div = document.createElement("div");
            div.className = "flex items-center gap-3";
            div.innerHTML = `
                <span class="text-gray-700 font-medium">Table ${tableCount}</span>
                <input type="number" name="seats[]" placeholder="Seats" required class="flex-1 border border-gray-300 rounded-xl px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-maroon">
            `;
            document.getElementById("tables").appendChild(div);
        }
    </script>

</body>
</html>
