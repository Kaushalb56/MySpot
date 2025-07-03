document.addEventListener("DOMContentLoaded", () => {
    loadTables();

    async function loadTables() {
        const res = await fetch("../php/get_tables.php");
        const tables = await res.json();
        const container = document.getElementById("table-container");
        container.innerHTML = "";

        tables.forEach(table => {
            const div = document.createElement("div");
            div.classList.add("table-card", table.status);
            div.innerHTML = `
                <div class="status-badge badge-${table.status}">${table.status.toUpperCase()}</div>
                <strong>Table ${table.table_number}</strong><br>
                ${table.seats} seats
            `;
            div.onclick = () => toggleStatus(table.id);
            container.appendChild(div);
        });
    }

    async function toggleStatus(tableId) {
        await fetch("../php/update_table_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `table_id=${tableId}`
        });
        loadTables();
    }
});
